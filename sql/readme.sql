Hey Kip,

The sql files you need to run are

1) changesonSql.sql
2) credit.sql /*Here am assuming that you have it as it was on the other site*/
3) fiance_purchases.sql  /*This will be the same files as that in the real easte*/

--  After you are done with the above sql then run 
4) Fiancail view.sql /*This will get you the following views, all transactions, Payables, Invoice and payment from patient and that also from creditors
The same shall be shown for recievables*/


/*Supporting files */
1) testsql.sql,sample.sql /*This is where i write the raw sql when its still in the development area*/
2)report.sql This is the well working sql that i use to create the views with , the are just cleanand can be used to test the data but wont create any view.

