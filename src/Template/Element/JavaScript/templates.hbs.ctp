<script id="wasabi-core-modal" type="text/template">
    <div class="modal-wrapper">
        <div class="<%= cssClasses.backdrop %>"></div>
        <div class="<%= cssClasses.scrollable %>">
            <div class="<%= cssClasses.container %>">
                <% if (hasHeader) { %>
                    <div class="<%= cssClasses.modalHeader %>">
                        <span><%= modalHeader %></span>
                        <% if (hasCloseLink) { %>
                            <a href="javascript:void(0)" data-dismiss="modal"><i class="icon-delete"></i></a>
                        <% } %>
                    </div>
                <% } %>

                <% if (hasBody) { %>
                    <div class="<%= cssClasses.modalBody %>">
                        <%= modalBody %>
                    </div>
                <% } %>

                <% if (hasFooter) { %>
                    <div class="<%= cssClasses.modalFooter %><% if (isConfirmModal) { %> <%= cssClasses.confirmFooter %><% } %>">
                        <form action="<%= action %>" method="<%= method %>">
                            <% if (isConfirmModal) { %>
                                <button class="button" type="submit"><span><%= confirmYes %></span></button>
                                <a href="javascript:void(0)" data-dismiss="modal"><%= confirmNo %></a>
                            <% } %>
                        </form>
                    </div>
                <% } %>
            </div>
        </div>
    </div>
</script>

<script id="wasabi-core-dialog" type="text/template">
    <div class="dialog-wrapper">
        <div class="dialog-inner<% if (sidebarLeft) { %> dialog-has-sidebar-left<% } %><% if (sidebarRight) { %> dialog-has-sidebar-right<% } %>">
            <header class="dialog-header">
                <h3><%= title %></h3>
                <a href="javascript:void(0)" data-toggle="close" title="<?= __d('wasabi_core', 'Close Dialog') ?>"><i class="icon-close"></i></a>
            </header>
            <% if (sidebarLeft) { %>
                <div class="dialog-sidebar dialog-sidebar-left"></div>
            <% } %>
            <% if (sidebarRight) { %>
                <div class="dialog-sidebar dialog-sidebar-right"></div>
            <% } %>
            <div class="dialog-content"></div>
            <div class="dialog-controls form-controls">
                <button type="submit" class="button green"><%= primaryAction %></button>
            </div>
        </div>
    </div>
</script>

<script id="wasabi-core-login-modal" type="text/template">
    <div class="modal-wrapper modal--login" tabindex="-1">
        <?= $this->Form->create(null, ['url' => ['plugin' => 'Wasabi/Core', 'controller' => 'Users', 'action' => 'login'], 'novalidate' => 'novalidate']) ?>
        <div class="modal-backdrop"></div>
        <div class="modal-scrollable">
            <div class="modal-container">
                <div class="modal-header">
                    <span><?= __d('wasabi_core', 'Login') ?></span>
                </div>
                <div class="modal-body">
                    <div class="flash-message flash-message--warning"><?= __d('wasabi_core', 'Your login session expired. Please login again.') ?></div>
                    <?= $this->element('Wasabi/Core.login-form-ajax') ?>
                </div>
                <div class="modal-footer modal-confirm">
                    <?= $this->Form->button('Login', ['class' => 'button blue']) ?>
                </div>
            </div>
        </div>
        <?= $this->Form->end() ?>
    </div>
</script>
