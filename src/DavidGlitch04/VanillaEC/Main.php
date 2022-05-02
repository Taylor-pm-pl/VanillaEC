<?php

namespace DavidGlitch04\VanillaEC;

use DavidGlitch04\VanillaEC\Enchantment\{
    BaneOfArthropodsEnchantment,
    FortuneEnchantment,
    LootingEnchantment,
    SmiteEnchantment
};
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\item\{
    Item,
    ItemIds,
    ItemFactory,
    ItemIdentifier,
    LegacyStringToItemParser,
    enchantment\ItemFlags,
    enchantment\StringToEnchantmentParser,
    enchantment\Rarity
};
use pocketmine\entity\{Entity, EntityFactory, Living};
use pocketmine\data\bedrock\{EnchantmentIdMap, EnchantmentIds, EntityLegacyIds};
use pocketmine\event\Listener;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\{EntityDamageByEntityEvent, EntityShootBowEvent};
use pocketmine\block\BlockLegacyIds as BlockIds;
use pocketmine\data\bedrock\EntityLegacyIds as EntityIds;

class Main extends PluginBase implements Listener{
	
	public const UNDEAD = [
        EntityIds::ZOMBIE,
        EntityIds::HUSK,
        EntityIds::WITHER,
        EntityIds::SKELETON,
        EntityIds::STRAY,
        EntityIds::WITHER_SKELETON,
        EntityIds::ZOMBIE_PIGMAN,
        EntityIds::ZOMBIE_VILLAGER
	];
	
	public const ARTHROPODS = [
        EntityIds::SPIDER,
        EntityIds::CAVE_SPIDER,
        EntityIds::SILVERFISH,
        EntityIds::ENDERMITE
	];
	
	public function onLoad(): void{
		$this->saveDefaultConfig();
		$enchantment = new FortuneEnchantment();
		$lt = new LootingEnchantment();
		$smite = new SmiteEnchantment();
		$boa = new BaneOfArthropodsEnchantment();
		EnchantmentIdMap::getInstance()->register($enchantment->getMcpeId(), $enchantment);
		EnchantmentIdMap::getInstance()->register($lt->getMcpeId(), $lt);
		EnchantmentIdMap::getInstance()->register($smite->getMcpeId(), $smite);
		EnchantmentIdMap::getInstance()->register($boa->getMcpeId(), $boa);
			
	}
	
	public function onEnable(): void{
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

    /**
     * @param BlockBreakEvent $event
     */
	public function onBreak(BlockBreakEvent $event) : void{
		$player = $event->getPlayer();
		$block = $event->getBlock();
		$item = $event->getItem();
		$enchantment = new FortuneEnchantment();
	
		if($block->getId() == ItemIds::LEAVES){
			if(mt_rand(1, 99) <= 10){
				$event->setDrops([ItemFactory::getInstance()->get(ItemIds::APPLE, 0, 1)]);
			}
		}
				
		if(($level = $item->getEnchantmentLevel(EnchantmentIdMap::getInstance()->fromId($enchantment->getMcpeId()))) > 0){
			$add = mt_rand(0, $level + 1);
					
			if($block->getId() == BlockIds::LEAVES){
				if(mt_rand(1, 99) <= 10){
					$event->setDrops([ItemFactory::getInstance()->get(ItemIds::APPLE, 0, 1)]);
				}
			}
			
			foreach($this->getConfig()->get("fortune.blocks", []) as $str){
				$it = LegacyStringToItemParser::getInstance()->parse($str);
				
				if($block->getId() == $it->getId()){
					if(mt_rand(1, 99) <= 10 * $level){
						if(empty($event->getDrops()) == false){
							$event->setDrops(array_map(function(Item $drop) use($add){
								$drop->setCount($drop->getCount() + $add);
								return $drop;
							}, $event->getDrops()));
						}
					}
					break;
				}
			}
		}
	}

    /**
     * @param EntityDamageByEntityEvent $event
     */
	public function onDamage(EntityDamageByEntityEvent $event) : void{
		$player = $event->getEntity();
		$damager = $event->getDamager();
		if($damager instanceof Player){
			$item = $damager->getInventory()->getItemInHand();
			$enchantment = new SmiteEnchantment();
			if($item->hasEnchantment(EnchantmentIdMap::getInstance()->fromId($enchantment->getMcpeId()))){
				if(in_array($player::getNetworkTypeId(), self::UNDEAD)){
					$event->setBaseDamage($event->getBaseDamage() + (2.5 * $item->getEnchantmentLevel(EnchantmentIdMap::getInstance()->fromId($enchantment->getMcpeId()))));
				}
			}
				$ench = new BaneOfArthropodsEnchantment();
			if($item->hasEnchantment(EnchantmentIdMap::getInstance()->fromId($ench->getMcpeId()))){
				if(in_array($player::getNetworkTypeId(), self::ARTHROPODS)){
					$event->setBaseDamage($event->getBaseDamage() + (2.5 * $item->getEnchantmentLevel(EnchantmentIdMap::getInstance()->fromId($ench->getMcpeId()))));
				}
			}
			 $en = new LootingEnchantment();
			 if(($level = $damager->getInventory()->getItemInHand()->getEnchantmentLevel(EnchantmentIdMap::getInstance()->fromId($en->getMcpeId()))) > 0){
			 	if($player instanceof Player == false and $player instanceof Living and $event->getFinalDamage() >= $player->getHealth()){
			 		$add = mt_rand(0, $level + 1);
					if(is_bool($this->getConfig()->get("looting.entities"))){
						$this->getLogger()->debug("There is an error (looting) in the config of vanillaEC");
						return;
					}
			 		foreach($this->getConfig()->get("looting.entities") as $eid => $items){
			 			$id = constant(EntityLegacyIds::class."::".strtoupper($eid));
			 			$drops = $this->getLootingDrops($player->getDrops(), $items, $add);
			 			foreach($drops as $drop){
			 				$damager->getWorld()->dropItem($player->getPosition()->asVector3(), $drop);
			 			}
			 			$player->flagForDespawn();
			 		}
			 	}
			 }
		}
	}

    /**
     * @param array $drops
     * @param array $items
     * @param int $add
     * @return array
     */
	 public function getLootingDrops(array $drops, array $items, int $add) : array{
	 	$r = [];
		
	 	foreach($items as $ite){
	 		$item = LegacyStringToItemParser::getInstance()->parse($ite);
	 		foreach($drops as $drop){
	 			if($drop->getId() == $item->getId()){
	 				$drop->setCount($drop->getCount() + $add);
	 			}
	 			$r[] = $drop;
	 			break;
	 		}
	 	}
	 	return $r;
	 }

    /**
     * @param EntityShootBowEvent $event
     */
	 public function onShoot(EntityShootBowEvent $event) : void{
	 	$arrow = $event->getProjectile();
	 	$bow = $event->getBow();
		
	 	if($arrow !== null and $arrow::getNetworkTypeId() == EntityIds::ARROW){
	 		$event->setForce($event->getForce() + 0.95); // In vanilla, arrows are fast
	 	}
	 }
}