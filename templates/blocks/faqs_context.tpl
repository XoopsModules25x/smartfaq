<ul>
    <{foreach item=contextfaq from=$block.faqs}>
        <li><a href="<{$xoops_url}>/modules/smartfaq/faq.php?faqid=<{$contextfaq.id}>"><{$contextfaq.question}></a></li>
    <{/foreach}>
</ul>
