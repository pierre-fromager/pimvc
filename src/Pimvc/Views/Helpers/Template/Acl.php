<script type="text/javascript">
    var targetId = '';
    var aclId = '';

    function processHeaders(show) {
            var headers = document.querySelectorAll('div.controler_header');
            for (i = 0; i < headers.length; ++i) {
                        headers[i].style.display = (show) ? 'block' : 'none';
                    }
                }


    $(document).ready(function () {

        $(".controler_header,.action_header").click(function () {
            $(this).toggleClass('active');
            $(this).toggleClass('inactive');
            targetId = $(this).attr('id') + '_content';
            $('#' + targetId).toggle();
        });

        $(".role_header").click(function () {
            aclId = $(this).attr('id');
            $.get('<?= $toggleUrl; ?>', {id: aclId}, function (data) {
                $('#link' + aclId).removeClass(data.acl_disable);
                $('#link' + aclId).addClass(data.acl_enable);
            });
        });

        $("#aclFilter").change(function () {
            filterValue = $(this).val();
            processHeaders(true);
            var headers = document.querySelectorAll('div.controler_header');
            for (i = 0; i < headers.length; ++i) {
                var headerName = headers[i].innerHTML.toLowerCase();
                var show = (headerName.indexOf(filterValue.toLowerCase()) !== -1);
                headers[i].style.display = (show) ? 'block' : 'none';
            }
        });
    });
</script>

<div class="acl-manager">
    <h2>
        <span class="fa fa-lock">&nbsp;</span>
        &nbsp;<?= $title; ?>
    </h2>
    <div class="input-group">
        <span id="basicAclFilterIcon" class="input-group-addon fa fa-filter">&nbsp;</span>
        <input 
            id="aclFilter" 
            title="Type then click icon filter to apply or just press enter"
            type="text" 
            class="form-control col-sm-12" 
            placeholder="Acl filter" 
            describedby="basicAclFilterIcon"
            />
        <span id="validateAclFilterIcon" class="input-group-addon fa fa-filter">&nbsp;</span>
    </div>
    <?php foreach ($ressources as $controllerName => $actions) : ?>
        <?php $shortCrtl = str_replace('\\', '_', $controllerName); ?>
        <div id="<?= $shortCrtl; ?>" class="controler_header inactive">
            <h3 class="controler_header"><?= $shortCrtl; ?></h3>
        </div>
        <div id="<?= $shortCrtl; ?>_content" class="controler_content">
            <?php foreach ($actions as $actionName => $roles) : ?>
                <div id="<?= $shortCrtl; ?>-<?= $actionName; ?>" class="action_header inactive">
                    <h4 class="controler_header"><?= $actionName; ?></h4>
                </div>
                <div id="<?= $shortCrtl . '-' . $actionName; ?>_content" class="action_content">
                    <?php foreach ($roles as $roleName => $acl) : ?>
                        <?php $id = $shortCrtl . '-' . $actionName . '-' . $roleName; ?>
                        <div id="<?= $id; ?>" class="role_header">
                            <a title="<?= str_replace('-', ' ', $id) . ' : ' . $acl; ?>" class="ajaxLink <?= $acl; ?>" href="#" id="link<?= $id; ?>">
                                <span class="role-acl"><?= $roleName; ?></span>
                            </a>
                        </div>
                        <div id="<?= $id; ?>_content" class="role_content"></div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
</div>