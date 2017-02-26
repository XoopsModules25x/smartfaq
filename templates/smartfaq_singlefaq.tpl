<div class="item">
    <div class="itemHead">
        <span class="itemTitle"><{$faq.fullquestionlink}></span>
    </div>
    <{if $op != 'preview'}>
        <div class="itemInfo">
            <span class="itemPoster">
                <{if $display_categoryname}>
                    <div class="sf_faq_head_cat">
                        [&nbsp;<a href="<{$xoops_url}>/modules/smartfaq/category.php?categoryid=<{$faq.categoryid}>"><{$faq.categoryname}></a>&nbsp;]
                    </div>
                <{/if}>
                <div class="sf_faq_head_who">
                    <{$faq.who_when}> (<{$faq.counter}> <{$lang_reads}>)
                </div>
            </span>
        </div>
    <{/if}>
    <div class="itemBody">
        <div class="itemText"><{$faq.answer}></div>
    </div>
    <br>

    <div class="itemInfo" style="height: 14px;">
        <{if $faq.cancomment && $faq.comments != -1}>
            <span style="float: left;"><a href="<{$xoops_url}>/modules/smartfaq/faq.php?faqid=<{$faq.faqid}>"><{$faq.comments}> <{$lang_comments}></a></span>
        <{else}>
            <span style="float: left;">&nbsp;</span>
        <{/if}>
        <span style="float: right; text-align: right;"><{$faq.adminlink}></span>
        <div style="height: 0; display: inline; clear: both;"></div>
    </div>
</div>
<br>
