<!-- Thank you for keeping this line in the template :-) //-->
<div style="font-size: 0;"><{$ref_smartfaq}></div>

<div class="sf_headertable">
    <span class="sf_modulename"><a href="<{$xoops_url}>/modules/<{$modulename}>/index.php"><{$whereInSection}></a></span><span class="sf_breadcrumb"> > <{$lang_submit}></span>
</div>

<{if $op == 'preview'}>
    <br>
    <{include file="db:smartfaq_singlefaq.tpl" faq=$faq}>
<{/if}>

<div class="sf_infotitle"><{$lang_intro_title}></div>
<div class="sf_infotext"><{$lang_intro_text}></div>
<br>
<{$form.javascript}>
<form name="<{$form.name}>" action="<{$form.action}>" method="<{$form.method}>" <{$form.extra}>>
    <table class="outer" cellspacing="1">
        <tr>
            <th colspan="2"><{$form.title}></th>
        </tr>
        <!-- start of form elements loop -->
        <{foreach item=element from=$form.elements}>
            <{if $element.hidden != true}>
                <tr>
                    <td class="head"><{$element.caption}>
                        <{if $element.description}>
                            <div style="font-weight: normal;"><{$element.description}></div>
                        <{/if}>
                    </td>
                    <td class="<{cycle values="even,odd"}>"><{$element.body}></td>
                </tr>
            <{else}>
                <{$element.body}>
            <{/if}>
        <{/foreach}>
        <!-- end of form elements loop -->
    </table>
</form>

<{if $isAdmin == 1}>
    <div class="sf_adminlinks"><{$sf_adminpage}></div>
<{/if}>
