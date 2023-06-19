<?php

namespace DavidGlitch04\VanillaEC\Enchantment;

use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\item\{Axe, enchantment\Enchantment, enchantment\ItemFlags, enchantment\Rarity, Item, Sword};
use pocketmine\lang\KnownTranslationFactory;

class BaneOfArthropodsEnchantment extends Enchantment
{
    use EnchantmentTrait;

    /**
     * BaneOfArthropodsEnchantment constructor.
     */
    public function __construct()
    {
        parent::__construct(
            KnownTranslationFactory::enchantment_damage_arthropods(),
            Rarity::UNCOMMON,
            ItemFlags::SWORD,
            ItemFlags::AXE,
            5
        );
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return "bane_of_arthropods";
    }

    /**
     * @return int
     */
    public function getMcpeId(): int
    {
        return EnchantmentIds::BANE_OF_ARTHROPODS;
    }

    /**
     * @return array
     */
    public function getIncompatibles(): array
    {
        return [EnchantmentIds::SHARPNESS, EnchantmentIds::SMITE];
    }

    /**
     * @param Item $item
     * @return bool
     */
    public function isItemCompatible(Item $item): bool
    {
        return $item instanceof Sword || $item instanceof Axe;
    }
}