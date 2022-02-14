<?php

namespace App\Classes;

use Illuminate\Support\Arr;

class PriceHelper
{
    /*
     * Todo: Coding Test for Technical Hires
     * Please read the instructions on the README.md
     * Your task is to write the functions for the PriceHelper class
     * A set of sample test cases and expected results can be found in PriceHelperTest
     */

    /**
     * Task: Given an associative array of minimum order quantities and their respective prices, write a function to return the unit price of an item based on the quantity.
     *
     * Question:
     * If I purchase 10,000 bicycles, the unit price of the 10,000th bicycle would be 1.50
     * If I purchase 10,001 bicycles, the unit price of the 10,001st bicycle would be 1.00
     * If I purchase 100,001 bicycles, what would be the unit price of the 100,001st bicycle?
     *
     * @param int $qty
     * @param array $tiers
     * @return float
     */
    public static function getUnitPriceTierAtQty(int $qty, array $tiers): float
    {
        $unitPrice = 0.0;

        if ($tiers && $qty > 0) {

            /* Get the array keys of each tier in order to get the next tier item on the iteration */
            $arrKeyTiers = array_keys($tiers);

            /* Return the minimum tier quantity of the pricing tiers based on the quantity  */
            $tierQty = Arr::first($arrKeyTiers, function($value, $index) use ($qty, $tiers, $arrKeyTiers) {
                $qtyStart = $value;

                /* get next item on the collection and identify the max quantity of the pricing tier */
                $nextTierIndex = $index + 1;
                if (isset($arrKeyTiers[$nextTierIndex])) {
                    $nextTierItem = $arrKeyTiers[$nextTierIndex];
                    $qtyEnd = $nextTierItem - 1;
                    return $qtyStart <= $qty && $qtyEnd >= $qty;
                }
                return $qtyStart <= $qty;
            });
            $unitPrice = $tiers[$tierQty];
        }

        return $unitPrice;
    }

    /**
     * Task: Given an associative array of minimum order quantities and their respective prices, write a function to return the total price of an order of items based on the quantity ordered
     *
     * Question:
     * If I purchase 10,000 bicycles, the total price would be 1.5 * 10,000 = $15,000
     * If I purchase 10,001 bicycles, the total price would be (1.5 * 10,000) + (1 * 1) = $15,001
     * If I purchase 100,001 bicycles, what would the total price be?
     *
     * @param int $qty
     * @param array $tiers
     * @return float
     */
    public static function getTotalPriceTierAtQty(int $qty, array $tiers): float
    {
        if ($tiers && $qty > 0) {
            $hashTable = [];

            $arrKeyTiers = array_keys($tiers);
            /* Create a collection based on the array key tiers and return a new set collection containing the min and max qty of the pricing tier */
            collect($arrKeyTiers)->mapWithKeys(function($minOrderQty,$index) use ($arrKeyTiers) {
                $nextTierIndex = $index + 1;
                $maxOrderQty = null;
                if (isset($arrKeyTiers[$nextTierIndex])) {
                    $maxOrderQty = $arrKeyTiers[$nextTierIndex] - 1;
                }
                return [$index => [
                    'min' => $minOrderQty,
                    'max' => $maxOrderQty,
                ]];
            })->each(function ($qtyTier) use ($qty, &$hashTable, $tiers) {
                if ($qtyTier['max']) {
                    /* calculate the total price on each pricing tier and based on the qty ordered */
                    if ($qty > $qtyTier['max']) {
                        $tierPrice = self::getUnitPriceTierAtQty($qtyTier['max'], $tiers);
                        $totalPrice = $tierPrice * $qtyTier['max'];
                    } else {
                        $prevMaxQty = $qtyTier['min'] > 0 ? $qtyTier['min'] - 1 : $qtyTier['min'];
                        $remainingQty = $qty > $prevMaxQty ? $qty - $prevMaxQty : $qty;
                        $tierPrice = self::getUnitPriceTierAtQty($qty, $tiers);
                        $totalPrice = $tierPrice * $remainingQty;
                    }

                    /* add the total price on a hashtable if it doesn't exist yet */
                    if (!in_array($totalPrice, $hashTable)) {
                        $hashTable[] = $totalPrice;
                    }
                }
            });

            return array_sum($hashTable);
        }
        return 0.0;
    }

    /**
     * Task: Given an array of quantity of items ordered per month and an associative array of minimum order quantities and their respective prices, write a function to return an array of total charges incurred per month. Each item in the array should reflect the total amount the user has to pay for that month.
     *
     * Question A:
     * A user purchased 933, 22012, 24791 and 15553 bicycles respectively in Jan, Feb, Mar, April
     * The management would like to know how much to bill this user for each of those month.
     * This user is on a special pricing tier where the quantity does not reset each month and is thus CUMULATIVE.
     *
     * Question B:
     * A user purchased 933, 22012, 24791 and 15553 bicycles respectively in Jan, Feb, Mar, April
     * The management would like to know how much to bill this user for each of those month.
     * This user is on the typical pricing tier where the quantity RESETS each month and is thus NOT CUMULATIVE.
     *
     */
    public static function getPriceAtEachQty(array $qtyArr, array $tiers, bool $cumulative = false): array
    {
       return [];
    }
}
