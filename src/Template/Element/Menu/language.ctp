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
				if ($lang->id === Configure::read('contentLanguage')->id) {
					$class = ' class="active"';
				}
                echo '<li' . $class . ' data-language-id="' . $lang->id . '">'
					. $this->Html->link($lang->iso2, $this->Route->languagesChange($lang->id))
				. '</li>';
			}
		}
		?>
	</ul>
</li>
