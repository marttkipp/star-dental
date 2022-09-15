CREATE OR REPLACE VIEW v_payroll AS
SELECT 
payroll.payroll_id,
(SELECT SUM(payroll_item.payroll_item_amount) FROM payroll_item,`table`
WHERE payroll_item.payroll_id = payroll.payroll_id AND `table`.table_id = payroll_item.table AND `table`.table_type = 1)
as total_additions,
(SELECT SUM(payroll_item.payroll_item_amount) FROM payroll_item,`table`
WHERE payroll_item.payroll_id = payroll.payroll_id AND `table`.table_id = payroll_item.table AND `table`.table_type = 2)
as total_deductions,
(
(SELECT SUM(payroll_item.payroll_item_amount) FROM payroll_item,`table`
WHERE payroll_item.payroll_id = payroll.payroll_id AND `table`.table_id = payroll_item.table AND `table`.table_type = 1) - 
(SELECT SUM(payroll_item.payroll_item_amount) FROM payroll_item,`table`
WHERE payroll_item.payroll_id = payroll.payroll_id AND `table`.table_id = payroll_item.table AND `table`.table_type = 2)
)
as total_payroll,
(SELECT SUM(payroll_item.payroll_item_amount) FROM payroll_item,`table`
WHERE payroll_item.payroll_id = payroll.payroll_id AND `table`.table_id = payroll_item.table AND `table`.table_type = 2 AND payroll_item.table = 11)
AS total_nssf,
(SELECT SUM(payroll_item.payroll_item_amount) FROM payroll_item,`table`
WHERE payroll_item.payroll_id = payroll.payroll_id AND `table`.table_id = payroll_item.table AND `table`.table_type = 2 AND payroll_item.table = 6)
AS total_loans,
(SELECT SUM(payroll_item.payroll_item_amount) FROM payroll_item,`table`
WHERE payroll_item.payroll_id = payroll.payroll_id AND `table`.table_id = payroll_item.table AND `table`.table_type = 2 AND payroll_item.table = 12)
AS total_nhif,
(SELECT SUM(payroll_item.payroll_item_amount) FROM payroll_item,`table`
WHERE payroll_item.payroll_id = payroll.payroll_id AND `table`.table_id = payroll_item.table AND `table`.table_type = 2 AND payroll_item.table = 9)
AS total_paye,
(SELECT SUM(payroll_item.payroll_item_amount) FROM payroll_item,`table`
WHERE payroll_item.payroll_id = payroll.payroll_id AND `table`.table_id = payroll_item.table AND `table`.table_type = 1 AND payroll_item.table = 10)
AS total_relief,
payroll.payroll_year,payroll.month_id,payroll.payroll_created_for
from payroll
WHERE payroll.payroll_closed = 1 AND payroll.payroll_status = 1 GROUP BY payroll.payroll_id
