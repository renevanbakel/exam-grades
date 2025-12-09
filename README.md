# Student Grade Calculator

A Symfony console application that reads Excel assignment result sheets and calculates grades and pass/fail status for students.

## Features

- Reads Excel files (`.xlsx`) containing student assignment results
- Calculates grades based on score ratios
- Determines pass/fail status
- Displays results in a formatted table with student IDs, scores, grades, and pass status

## Requirements

- PHP >= 8.4
- Composer

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/renevanbakel/exam-grades
   ```

2. Install dependencies:
   ```bash
   composer install
   ```

## Usage

Run the command with the path to your Excel file:

```bash
php bin/console app:calculate-grades <file>
```

### Example

```bash
php bin/console app:calculate-grades data/Assignment.xlsx
```

### Command Arguments

- `file` (required): Path to the Excel file (e.g., `data/Assignment.xlsx`)

## Excel File Format

The Excel file should follow this structure:

- **Row 0**: Column headers (e.g., "Student ID", "Question 1", "Question 2", etc.)
- **Row 1**: Maximum scores per question (e.g., 10, 5, 15, etc.)
- **Row 2+**: Student data
  - First column: Student ID
  - Subsequent columns: Scores for each question

### Example Excel Structure

| Student ID | Q1 | Q2 | Q3 |
|------------|----|----|----|
| Max Score  | 10 | 5  | 15 |
| 12345      | 8  | 4  | 12 |
| 67890      | 9  | 5  | 14 |

## Grading Rules

Grades are calculated based on the ratio of the student's total score to the maximum possible score:

- **≤ 20%**: Grade = 1.0
- **≥ 70% and < 100%**: Grade = 5.5
- **≥ 100%**: Grade = 10.0

### Pass/Fail Status

- **Pass**: Score ratio ≥ 70%
- **Fail**: Score ratio < 70%

## Output

The command displays a formatted table with the following columns:

- **Student ID**: The student's identifier
- **Score**: Total score achieved
- **Grade**: Calculated grade (1.0, 5.5, or 10.0)
- **Passed**: YES 🎉 or NO ❌

### Example Output

```
Loading results from Excel...
Processing results for 5 rows
✓ Grades successfully calculated!

┌────────────┬───────┬───────┬────────┐
│ Student ID │ Score │ Grade │ Passed │
├────────────┼───────┼───────┼────────┤
│ 12345      │ 24    │ 5.5   │ YES 🎉 │
│ 67890      │ 28    │ 10.0  │ YES 🎉 │
└────────────┴───────┴───────┴────────┘
```

## Project Structure

```
assessment/
├── bin/
│   └── console          # Symfony console executable
├── config/              # Symfony configuration files
├── data/                # Sample Excel files
├── src/
│   ├── Command/
│   │   └── GradeCommand.php        # Main console command
│   ├── DTO/
│   │   └── StudentGradeDTO.php     # Data transfer object
│   └── Service/
│       ├── ExcelReaderService.php        # Excel file reading
│       └── GradeCalculatorService.php    # Grade calculation logic
├── composer.json
└── README.md
```

## Dependencies

- **symfony/console**: Console component for CLI commands
- **symfony/framework-bundle**: Symfony framework bundle
- **phpoffice/phpspreadsheet**: Excel file reading and writing

## Development

### Code Style

This project uses PHP 8.4+ features including:
- Strict types declaration
- Readonly properties
- Match expressions
- Named arguments
