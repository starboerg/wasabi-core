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
                echo '<li' . $class . ' data-language-id="' . $lang->id . '">'
					. $this->Html->link($lang->iso2, [
						'plugin' => 'Wasabi/Core',
						'controller' => 'Languages',
						'action' => 'switch',
						$lang->id
					])
				. '</li>';
			}
		}
		?>
	</ul>
</li>