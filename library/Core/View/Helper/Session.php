<?php

/**
 * Session js scripts manager
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class Core_View_Helper_Session extends Core_View_Helper_View
{
    public $view;

    public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;
    }

    public function session()
    {
        $host = $this->view->domainLink(1);
        $user = Core_Model_User::getInstance();
        if ($user->timeout > 0 && $user->rememberme == 0) {

            ?>
            <script type="text/javascript">
                $(document).ready(function () {
                    $('#session_timer').countdown({
                        until: +<?php echo $user->timeout?>,
                        format: 'MS',
                        layout: '<div id="t7_timer">' +
                        '<div id="t7_vals">' +
                        '<div id="t7_m" class="t7_numbs">{mnn}:</div>' +
                        '<div id="t7_s" class="t7_numbs">{snn}</div><div class="clear"></div>' +
                        '</div>' +
                        '<div id="t7_timer_over"></div>' +
                        '</div>',
                        onExpiry: function () {
                            $.ajax({
                                url: "<?php echo $host . 'admin/auth/logout'; ?>",
                                type: "GET",
                                dataType: "html",
                                complete: function (html, st) {
                                }
                            });

                            $('#endSession').foundation('reveal', 'open');
                        }
                    });
                });
            </script>
            <?php
        }
    }
}
     