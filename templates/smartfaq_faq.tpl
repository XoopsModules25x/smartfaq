<!-- Thank you for keeping this line in the template :-) //-->
<div style="display: none;"><{$ref_smartfaq}></div>

<div class="sf_headertable">
    <span class="sf_modulename"><a
                href="<{$xoops_url}>/modules/<{$modulename}>/index.php"><{$sectionname}></a></span><span
            class="sf_breadcrumb"> &gt; <{$faq.categoryPath}></span>
</div>

<br>

<{include file="db:smartfaq_singlefaq.tpl" faq=$faq}>
<!--next line is to include smarttie -->
<{if $smarttie==1}>
    <{include file='db:smarttie_links.tpl'}>
<{/if}>
<!--end smarttie -->
<{if $isAdmin == 1}>
    <div class="sf_adminlinks"><{$sf_adminpage}></div>
<{/if}>


<table border="0" width="100%" cellspacing="1" cellpadding="0" align="center">
    <tr>
        <td colspan="3" align="left">
            <div style="text-align: center; padding: 3px;
        margin:3px;"> <{$commentsnav}> <{$lang_notice}></div>
            <div style="margin:3px; padding: 3px;">
                <!-- start comments loop -->
                <{if $comment_mode == "flat"}> <{include file="db:system_comments_flat.tpl"}>
                <{elseif $comment_mode == "thread"}> <{include file="db:system_comments_thread.tpl"}>
                <{elseif $comment_mode == "nest"}> <{include file="db:system_comments_nest.tpl"}>
                <{/if}>
                <!-- end comments loop -->
            </div>
        </td>
    </tr>
</table>


<{include file='db:system_notification_select.tpl'}>
