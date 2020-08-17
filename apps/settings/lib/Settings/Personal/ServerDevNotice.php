<?php
/**
 * @copyright 2016, Roeland Jago Douma <roeland@famdouma.nl>
 *
 * @author Julius Härtl <jus@bitgrid.net>
 * @author Roeland Jago Douma <roeland@famdouma.nl>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\Settings\Settings\Personal;

use OCA\Viewer\Event\LoadViewer;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\EventDispatcher\IEventDispatcher;
use OCP\Files\IRootFolder;
use OCP\IUserSession;
use OCP\Settings\ISettings;
use OCP\Support\Subscription\IRegistry;
use OCP\Util;

class ServerDevNotice implements ISettings {

	/** @var IRegistry */
	private $registry;

	/** @var IEventDispatcher */
	private $eventDispatcher;

	/** @var IRootFolder */
	private $rootFolder;

	/** @var IUserSession */
	private $userSession;

	public function __construct(IRegistry $registry,
								IEventDispatcher $eventDispatcher,
								IRootFolder $rootFolder,
								IUserSession $userSession) {
		$this->registry = $registry;
		$this->eventDispatcher = $eventDispatcher;
		$this->rootFolder = $rootFolder;
		$this->userSession = $userSession;
	}

	/**
	 * @return TemplateResponse
	 */
	public function getForm() {
		$userFolder = $this->rootFolder->getUserFolder($this->userSession->getUser()->getUID());

		// If the Reasons to use Nextcloud.pdf file is here, let's init Viewer
		if ($userFolder->nodeExists('Reasons to use Nextcloud.pdf')) {
			$this->eventDispatcher->dispatch(LoadViewer::class, new LoadViewer());
			Util::addScript('settings', 'settings-nextcloud-pdf');
		}

		return new TemplateResponse('settings', 'settings/personal/development.notice', ['has-reasons-use-nextcloud-pdf' => true]);
	}

	/**
	 * @return string the section ID, e.g. 'sharing'
	 */
	public function getSection() {
		if ($this->registry->delegateHasValidSubscription()) {
			return null;
		}

		return 'personal-info';
	}

	/**
	 * @return int whether the form should be rather on the top or bottom of
	 * the admin section. The forms are arranged in ascending order of the
	 * priority values. It is required to return a value between 0 and 100.
	 *
	 * E.g.: 70
	 */
	public function getPriority() {
		return 1000;
	}
}
