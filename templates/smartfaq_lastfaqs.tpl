<{if $displaycollaps == 1}>
    <div class="sf_collaps_title"><a href='#' onclick="toggle('bottomtable'); toggleIcon('bottomtableicon');"><img id='bottomtableicon' src='<{$xoops_url}>/modules/smartfaq/assets/images/icon/close12.gif' alt=''/></a>&nbsp;<{$lang_index_faqs}></div>
<div id='bottomtable'>
    <span class="sf_collaps_info"><{$lang_index_faqs_info}></span>
    <!-- Content under the collapsable bar //-->
    <{/if}>
    <div align="right"><{$navbar}></div>
    <{if $displayFull == 1}>
        <!-- Start faq loop -->
        <{foreach item=faq from=$faqs}>
            <{include file="db:smartfaq_singlefaq.tpl" faq=$faq}>
        <{/foreach}>
        <!-- End faq loop -->
    <{else}>
        <table border="0" width="90%" cellspacing="1" cellpadding="3" align="center" class="outer">
            <tr>
                <td align="left" class="itemHead" width='65%'><b><{$lang_faq}></b></td>
                <{if $display_date_col == 1}>
                    <td align="center" class="itemHead" width="25%"><b><{$lang_datesub}></b></td>
                <{/if}>
                <{if $display_hits_col == 1}>
                    <td align="center" class="itemHead" width="10%"><b><{$lang_hits}></b></td>
                <{/if}>
            </tr>
            <!-- Start faq loop -->
            <{foreach item=faq from=$faqs}>
                <tr valign="top">
                    <td class="even" align="left"><{$faq.questionlink}></b></td>
                    <{if $display_date_col == 1}>
                        <td class="odd" align="left">
                            <div align="center" valign="middle"><{$faq.datesub}></div>
                        </td>
                    <{/if}>
                    <{if $display_hits_col == 1}>
                        <td class="odd" align="left">
                            <div align="center"><{$faq.counter}></div>
                        </td>
                    <{/if}>
                </tr>
            <{/foreach}>
            <!-- End faq loop -->
            <tr>
            </tr>
        </table>
        <br/>
    <{/if}>
    <div align="right"><{$navbar}></div>
    <{if $displaycollaps == 1}>
</div>
<{/if}>
