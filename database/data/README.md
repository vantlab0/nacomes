# Department Student Register

`computer_engineering_students.csv` contains the matriculation numbers that may
register for the NACOMES election portal. It was compiled from the department's
100, 200, and 300 level attendance sheets.

Columns:

- `name` — student name as printed on the attendance sheet (may be blank)
- `matric_no` — normalized matriculation number, unique per student
- `department` — department name
- `level` — level inferred from the matriculation year

Import or refresh the register with:

```sh
php artisan db:seed --class=DepartmentStudentSeeder
```

The seeder upserts by `matric_no`, so it is safe to run repeatedly after adding
new students.

## Admin upload

Administrators can also manage the register from **Eligible Matrics** in the
admin console. Upload a CSV with a `matric_no` column and optional `name` and
`level` columns, or a TXT file with one matriculation number per line. Uploads
are limited to 2 MB and 5,000 rows. Existing records are updated by
`matric_no`; blank name or level values preserve the details already on record.

Known source gaps:

- `COET/10977/2023` has no name in the 300 level sheet.
- The 100 level sheet entry for OVIEMUNO PRECIOUS OGHENEOVO has no matric number.
- No 400 level attendance sheet was provided. Only `COET/9412/2022` is present
  because it appeared on the 300 level sheet.
