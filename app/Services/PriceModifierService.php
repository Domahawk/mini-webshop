<?php

namespace App\Services;

use App\Enums\PriceModifier;
use App\Exceptions\BadRequestExceptions\UnsupportedPriceModifier;
use App\Models\Order;
use App\Models\OrderPriceModifier;
use Illuminate\Support\Facades\Auth;

class PriceModifierService
{
    /**
     * @throws UnsupportedPriceModifier
     */
    public function applyPriceModifiers(Order $order, array|null $modifiers): void
    {
        if (!empty($modifiers)) {
            $this->applyAddableModifiers($modifiers, $order);
            $this->calculateOrderTotal($order);
        }

        // apply 10 percent discount if order total is still above 100
        if ($order->total > 100) {
            $modifier = $this->getPriceModifier($order,PriceModifier::LARGE_ORDER_DISCOUNT);
            $order->priceModifiers()->save($modifier);
            $order->total = $order->total - $modifier->real_amount;
        }

        $vat = $this->getPriceModifier($order, PriceModifier::VAT);
        $order->priceModifiers()->save($vat);
        $order->vat = $vat->real_amount;
        $order->total_vat = $order->total + $vat->real_amount;
    }

    /**
     * @throws UnsupportedPriceModifier
     */
    private function applyAddableModifiers(array $modifiers, Order $order): void
    {
        foreach ($modifiers as $modifier) {
            $priceModifier = PriceModifier::create($modifier);

            if ($priceModifier->isUndefined() || !$priceModifier->isApplicableByUser()) {
                throw new UnsupportedPriceModifier($modifier);
            }

            $order->priceModifiers()->save($this->getPriceModifier($order, $priceModifier));
        }
    }

    private function getPriceModifier(Order $order, PriceModifier $priceModifierEnum): OrderPriceModifier
    {
        $modifierAmount = $this->getModifierAmount($priceModifierEnum);
        $priceModifier = new OrderPriceModifier([
            'name' => $priceModifierEnum->value,
            'amount' => $modifierAmount,
            'type' => $priceModifierEnum->getModifierType()->value,
            'real_amount' => $modifierAmount
        ]);

        if ($priceModifierEnum->getModifierType()->isPercent()) {
            $priceModifier->real_amount = round($order->total * ($modifierAmount / 100), 2, PHP_ROUND_HALF_DOWN);
        }

        return $priceModifier;
    }

    private function getModifierAmount(PriceModifier $priceModifier): int
    {
        if ($priceModifier->isVat()) {
            $user = Auth::user();
            $vat = $user?->address?->state?->vat;

            if (empty($vat)) {
                $vat = $user?->address?->country->vat;
            }

            if (empty($vat)) {
                $vat = PriceModifier::VAT->getModifierAmount();
            }

            return $vat;
        }

        return $priceModifier->getModifierAmount();
    }

    private function calculateOrderTotal(Order $order): void
    {
        $priceModifiers = $order->priceModifiers;

        foreach ($priceModifiers as $priceModifier) {
            $order->total -= $priceModifier->real_amount;
        }
    }
}
