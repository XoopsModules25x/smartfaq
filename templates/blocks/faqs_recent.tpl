<table class="outer" cellspacing="1">

    <tr>
        <td class="head"><{$block.lang_question}></td>
        <td class="head" align="left"><{$block.lang_category}></td>
        <td class="head" align="center" width="100px"><{$block.lang_poster}></td>
        <td class="head" align="right" width="120"><{$block.lang_date}></td>
    </tr>

    <{foreach item=faq from=$block.faqs}>
        <tr class="<{cycle values="even,odd"}>">
            <td><a href="<{$xoops_url}>/modules/smartfaq/faq.php?faqid=<{$faq.faqid}>"><{$faq.question}></a></td>
            <td align="left"><a
                        href="<{$xoops_url}>/modules/smartfaq/category.php?categoryid=<{$faq.categoryid}>"><{$faq.categoryname}></a>
            <td align="center"><{$faq.poster}></td>
            <td align="right"><{$faq.date}></td>
        </tr>
    <{/foreach}>

</table>

<div style="text-align:right; padding: 5px;">
    <a href="<{$xoops_url}>/modules/smartfaq/"><{$block.lang_visitfaq}></a>
</div>
