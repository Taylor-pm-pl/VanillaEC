<?php

namespace DavidGlitch04\VanillaEC\Enchantment;

use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\item\{enchantment\Enchantment, enchantment\ItemFlags, enchantment\Rarity, Item, Sword};
use pocketmine\lang\KnownTranslationFactory;

class LootingEnchantment extends Enchantment
{
    use EnchantmentTrait;

    /**
     * LootingEnchantment constructor.
     */
    public function __construct()
    {
        parent::__construct(
            KnownTranslationFactory::enchantment_lootBonus(),
            Rarity::RARE,
            ItemFlags::SWORD,
            ItemFlags::NONE,
            3
        );
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return "looting";
    }

    /**
     * @return int
     */
    public function getMcpeId(): int
    {
        return EnchantmentIds::LOOTING;
    }

    /**
     * @param Item $item
     * @return bool
     */
    public function isItemCompatible(Item $item): bool
    {
        return $item instanceof Sword;
    }
}