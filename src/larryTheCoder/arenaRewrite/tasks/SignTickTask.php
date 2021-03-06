<?php
/**
 * Adapted from the Wizardry License
 *
 * Copyright (c) 2015-2019 larryTheCoder and contributors
 *
 * Permission is hereby granted to any persons and/or organizations
 * using this software to copy, modify, merge, publish, and distribute it.
 * Said persons and/or organizations are not allowed to use the software or
 * any derivatives of the work for commercial use or any other means to generate
 * income, nor are they allowed to claim this software as their own.
 *
 * The persons and/or organizations are also disallowed from sub-licensing
 * and/or trademarking this software without explicit permission from larryTheCoder.
 *
 * Any persons and/or organizations using this software must disclose their
 * source code and have it publicly available, include this license,
 * provide sufficient credit to the original authors of the project (IE: larryTheCoder),
 * as well as provide a link to the original project.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,FITNESS FOR A PARTICULAR
 * PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
 * TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE
 * USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace larryTheCoder\arenaRewrite\tasks;

use larryTheCoder\arenaRewrite\Arena;
use larryTheCoder\SkyWarsPE;
use larryTheCoder\utils\Utils;
use pocketmine\block\WallSign;
use pocketmine\math\Vector3;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\tile\Sign;

class SignTickTask extends Task {

	/** @var Arena */
	private $arena;
	private $updateTime;

	public function __construct(Arena $arena){
		$this->arena = $arena;
	}

	/**
	 * Actions to execute when run
	 *
	 * @param int $currentTick
	 *
	 * @return void
	 */
	public function onRun(int $currentTick){
		$this->updateTime++;
		if($this->updateTime >= $this->arena->statusLineUpdate){
			$vars = ['%alive', '%status', '%max', '&', '%world', '%prefix', '%name'];
			$replace = [count($this->arena->getPlayers()), $this->arena->getStatus(), $this->arena->maximumPlayers, "§", $this->arena->arenaWorld, SkyWarsPE::getInstance()->getPrefix(), $this->arena->arenaName];
			$level = Server::getInstance()->getLevelByName($this->arena->joinSignWorld);
			if($level === null){
				goto skipUpdate;
			}
			$tile = $level->getTile(new Vector3($this->arena->joinSignX, $this->arena->joinSignY, $this->arena->joinSignZ));
			if($tile instanceof Sign){
				$block = $tile->getLevel()->getBlock($tile);
				if($block instanceof WallSign){
					$vec = $block->getSide($block->getDamage() ^ 0x01);
					$tile->getLevel()->setBlock($vec, Utils::getBlockStatus($this->arena));
				}
				$tile->setText(str_replace($vars, $replace, $this->arena->statusLine1), str_replace($vars, $replace, $this->arena->statusLine2), str_replace($vars, $replace, $this->arena->statusLine3), str_replace($vars, $replace, $this->arena->statusLine4));
			}
			skipUpdate:
			$this->updateTime = 0;
		}
	}
}