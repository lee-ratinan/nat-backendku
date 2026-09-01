<h5>SUMMARY</h5>
<table class="table table-sm table-hover">
    <tr><td>MONTHLY INCOME</td><td class="text-end"><?= currency_format($currency_code, $monthly_income) ?></td></tr>
    <tr><td>ANNUAL INCOME</td><td class="text-end"><?= currency_format($currency_code, $annual_income) ?></td></tr>
    <tr><td>GENERIC DEDUCTION</td><td class="text-end">-<?= currency_format($currency_code, $tax_details['deduction']) ?></td></tr>
    <tr><td>TAXABLE INCOME</td><td class="text-end"><?= currency_format($currency_code, $tax_details['taxable_income']) ?></td></tr>
</table>
<h5>CALCULATION</h5>
<table class="table table-sm table-hover">
    <thead>
    <tr>
        <th colspan="2">TAXABLE INCOME</th>
        <th>RATE</th>
        <th>AMOUNT</th>
        <th>subtotal</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($tax_details['lines'] as $line) : ?>
        <tr>
            <td class="text-end"><?= (0 == $line['prev_limit'] ? 'NIL' : currency_format($currency_code, $line['prev_limit'])) ?></td>
            <td class="text-end"><?= (PHP_INT_MAX == $line['limit'] ? 'ABOVE' : currency_format($currency_code, $line['limit'])) ?></td>
            <td class="text-end"><?= number_format($line['rate'], 2) ?>%</td>
            <td class="text-end"><?= currency_format($currency_code, $line['amount']) ?></td>
            <td class="text-end"><?= currency_format($currency_code, $line['subtotal']) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot>
    <tr>
        <td colspan="4" class="text-end">TOTAL TAX</td>
        <td class="text-end"><?= currency_format($currency_code, $tax_details['total']) ?></td>
    </tr>
    </tfoot>
</table>
<h5>TAX</h5>
<table class="table table-sm table-hover">
    <tr><td>TOTAL TAX</td><td class="text-end"><?= currency_format($currency_code, $tax_details['total']) ?></td></tr>
    <tr><td>NET INCOME (AFTER TAX)</td><td class="text-end"><?= currency_format($currency_code, $after_tax) ?></td></tr>
</table>