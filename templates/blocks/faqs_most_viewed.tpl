<ul>
    <{foreach item=newfaqs from=$block.newfaqs}>
        <li><a href="<{$xoops_url}>/modules/smartfaq/faq.php?faqid=<{$newfaqs.id}>"><{$newfaqs.linktext}></a>
            &nbsp;[<{$newfaqs.new}>]
        </li>
    <{/foreach}>
</ul>
