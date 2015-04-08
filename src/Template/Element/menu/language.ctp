<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 */

use \Cake\Core\Configure;
?>
<li class="lang-switch">
	<ul>
		<?php
		$frontendLanguages = Configure::read('languages.frontend');
		if (!empty($frontendLanguages)) {
			/** @var \Wasabi\Core\Model\Entity\Language $lang */
			foreach ($frontendLanguages as $lang) {
				$class = '';
				if ($lang->id == Configure::read('Wasabi.content_language.id')) {
					$class = ' class="active"';
				}
				echo "<li${class}>" . $this->Html->backendLink($lang->iso2, '/backend/languages/switch/' . $lang->id) . "</li>";
			}
		}
		?>
	</ul>
</li>