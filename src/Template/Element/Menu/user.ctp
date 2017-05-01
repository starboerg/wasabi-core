<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 */
use Wasabi\Core\Wasabi;

if (Wasabi::user() !== null): ?>
	<li class="user-menu dropdown">
		<a data-toggle="dropdown" href="javascript:void(0)"><?= Wasabi::user()->fullName() ?><i class="icon-arrow-down"></i></a>
		<ul class="dropdown-menu dropdown-menu-right">
			<li>
                <?= $this->Guardian->protectedLink(
				    __d('wasabi_core', 'Edit Profile'),
				    $this->Route->profile()
			    ) ?>
            </li>
			<li>
                <?= $this->Html->link(
				    __d('wasabi_core', 'Logout'),
				    $this->Route->logout()
			    ) ?>
            </li>
		</ul>
	</li>
<?php endif; ?>
