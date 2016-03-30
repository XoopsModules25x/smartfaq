<ul>
    <{foreach item=newfaqs from=$block.newfaqs}>
        <li><a href="<{$xoops_url}>/modules/smartfaq/answer.php?faqid=<{$newfaqs.id}>"><{$newfaqs.linktext}></a>
            <{if $newfaqs.show_date}>
                &nbsp;[<{$newfaqs.new}>]
            <{/if}>
        </li>
    <{/foreach}>
    <li><a href="<{$xoops_url}>/modules/smartfaq/open_index.php"><{$block.lang_allunanswered}></a></li>
</ul>
