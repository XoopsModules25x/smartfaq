<!-- Thank you for keeping this line in the template :-) //-->
<div style="display: none;"><{$ref_smartfaq}></div>

<div class="sf_headertable">
    <span class="sf_modulename"><{$sectionname}></span><span class="sf_breadcrumb">&nbsp;</span>
</div>

<{if $displaycollaps == 1}>
    <script type="text/javascript"><!--
        function goto_URL(object) {
            window.location.href = object.options[object.selectedIndex].value;
        }

        function toggle(id) {
            if (document.getElementById) {
                obj = document.getElementById(id);
            }
            if (document.all) {
                obj = document.all[id];
            }
            if (document.layers) {
                obj = document.layers[id];
            }
            if (obj) {
                if (obj.style.display === "none") {
                    obj.style.display = "";
                } else {
                    obj.style.display = "none";
                }
            }
            return false;
        }

        var iconClose = new Image();
        iconClose.src = 'assets/images/icon/close12.gif';
        var iconOpen = new Image();
        iconOpen.src = 'assets/images/icon/open12.gif';

        function toggleIcon(iconName) {
            if (document.images[iconName].src == window.iconOpen.src) {
                document.images[iconName].src = window.iconClose.src;
            } else if (document.images[iconName].src == window.iconClose.src) {
                document.images[iconName].src = window.iconOpen.src;
            }
            return;
        }

        //-->
    </script>
<{/if}>

<{if $lang_mainintro != ''}>
    <span class="sf_infotitle"><{$lang_mainhead}></span>
    <span class="sf_infotext"><{$lang_mainintro}></span>
    <br>
<{/if}>


<{if $displaycollaps == 1}>
    <div class="sf_collaps_title"><a href='#' onclick="toggle('toptable'); toggleIcon('toptableicon')" ;><img
                    id='toptableicon' src='<{$xoops_url}>/modules/smartfaq/assets/images/icon/close12.gif'
                    alt=''></a>&nbsp;<{$lang_categories_summary}></div>
<div id='toptable'>
    <span class="sf_collaps_info"><{$lang_categories_summary_info}></span>

    <!-- Content under the collapsable bar //-->
    <{else}>
    <br>
    <{/if}>
    <div align="right"><{$catnavbar}></div>
    <table border="0" width="90%" cellspacing="1" cellpadding="0" align="center" class="outer">
        <tr>
            <td align="left" class="itemHead"><b><{$lang_category}></b></td>
            <td align="center" width="50px" class="itemHead"><b><{$lang_smartfaqs}></b></td>
            <{if $displaylastfaq == 1}>
                <td align="right" width="40%" class="itemHead"><b><{$lang_last_smartfaq}></b></td>
            <{/if}>
        </tr>
        <!-- Start categories loop -->
        <{foreach item=category from=$categories}>
            <tr>
                <td valign="middle" class="even" align="left">
                    <{if $isAdmin == 1}>
                        <a href="<{$xoops_url}>/modules/smartfaq/admin/category.php?op=mod&categoryid=<{$category.categoryid}>"><img src="<{$xoops_url}>/modules/smartfaq/assets/images/icon/cat.gif"
                                    title="<{$lang_editcategory}>" alt="<{$lang_editcategory}>"></a>
                        &nbsp;
                        <b><{$category.categorylink}></b>
                    <{else}>
                        <img src="<{$xoops_url}>/modules/smartfaq/assets/images/icon/cat.gif" alt="">
                        &nbsp;
                        <b><{$category.categorylink}></b>
                    <{/if}>

                    <br>
                    <{if $displaytopcatdsc}>
                        <span class="sf_category_dsc"><{$category.description}></span>
                    <{/if}>
                </td>
                <td valign="middle" class="even" align="center"><{$category.total}></td>
                <{if $displaylastfaq == 1}>
                    <td valign="middle" class="even" align="right"><{$category.last_question_link}></td>
                <{/if}>
            </tr>
            <{foreach item=subcat from=$category.subcats}>
                <tr>
                    <td valign="middle" class="odd" align="left">
                        <div style="padding-left: 10px;">
                            <{if $isAdmin == 1}>
                                <a href="<{$xoops_url}>/modules/smartfaq/admin/category.php?op=mod&categoryid=<{$subcat.categoryid}>"><img src="<{$xoops_url}>/modules/smartfaq/assets/images/icon/subcat.gif"
                                            title="<{$lang_editcategory}>" alt="<{$lang_editcategory}>"
                                            alt="<{$lang_editcategory}>"></a>
                                &nbsp;<{$subcat.categorylink}>
                            <{else}>
                                <img src="<{$xoops_url}>/modules/smartfaq/assets/images/icon/subcat.gif"
                                     title="<{$lang_editcategory}>" alt="<{$lang_editcategory}>">
                                &nbsp;<{$subcat.categorylink}>
                            <{/if}>
                            <{if $displaysubcatdsc == 1}>
                                <span class="sf_category_dsc"><{$subcat.description}></span>
                            <{/if}>
                        </div>
                    </td>
                    <td valign="middle" class="odd" align="center"><{$subcat.total}></td>
                    <{if $displaylastfaq == 1}>
                        <td valign="middle" class="odd" align="right"><{$subcat.last_question_link}></td>
                    <{/if}>
                </tr>
            <{/foreach}>

        <{/foreach}>
        <!-- End categories loop -->
        <tr>
        </tr>
    </table>
    <div align="right"><{$catnavbar}></div>
    <{if $displaycollaps == 1}>
</div>
<{/if}>
<br>

<{if $displaylastfaqs}>
    <{include file="db:smartfaq_lastfaqs.tpl"}>
<{/if}>

<{if $isAdmin == 1}>
    <div class="sf_adminlinks"><{$sf_adminpage}></div>
<{/if}>


<{include file='db:system_notification_select.tpl'}>
