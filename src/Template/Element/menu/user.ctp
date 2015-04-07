<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 */
if ($this->Session->check('Auth.User.id')): ?>
	<li class="user-menu dropdown">
		<a data-toggle="dropdown" href="javascript:void(0)"><?= $this->Session->read('Auth.User.username') ?><i class="icon-arrow-down"></i></a>
		<ul class="dropdown-menu dropdown-menu-right">
			<li><?= $this->Html->backendUnprotectedLink(__d('wasabi_core', 'Edit Profile'), '/backend/profile') ?></li>
			<li><?= $this->Html->backendUnprotectedLink(__d('wasabi_core', 'Logout'), '/backend/logout') ?></li>
		</ul>
	</li>
<?php endif; ?>