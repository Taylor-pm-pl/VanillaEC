<?php

namespace DavidGlitch04\VanillaEC\Enchantment;

use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\item\{enchantment\Enchantment, enchantment\ItemFlags, enchantment\Rarity, Item, Tool};
use pocketmine\lang\KnownTranslationFactory;

class FortuneEnchantment extends Enchantment{
    use EnchantmentTrait;

    /**
     * FortuneEnchantment constructor.
     */
    public function __construct(){
        parent::__construct(
            KnownTranslationFactory::enchantment_lootBonusDigger(),
            Rarity::RARE,
            ItemFlags::DIG,
            ItemFlags::NONE,
            3
        );
    }

    public function getId(): string{
        return "fortune";
    }

    /**
     * @return int
     */
    public function getMcpeId(): int{
        return EnchantmentIds::FORTUNE;
    }

    /**
     * @return array
     */
    public function getIncompatibles(): array{
        return [EnchantmentIds::SILK_TOUCH];
    }

    /**
     * @param Item $item
     * @return bool
     */
    public function isItemCompatible(Item $item): bool{
        return $item instanceof Tool;
    }
}