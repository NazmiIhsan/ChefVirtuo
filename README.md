# ChefVirtuo

ChefVirtuo is a Laravel 12 lecturer dashboard for monitoring quiz results from a VR culinary training application.

## Included

- Google-only Firebase Authentication
- Lecturer email allow-list middleware
- Firestore service for the `quizResults` collection
- Dashboard cards for total students, average, highest, and lowest score
- Search by student ID, module filter, PASS/FAIL filter
- Chart.js analytics for score bars, pass/fail ratio, and module comparison
- Blade templates styled with Tailwind CSS and a warm culinary/VR visual system

## Setup

1. Install PHP dependencies.

```bash
composer install
```

2. Install frontend dependencies.

```bash
npm install
```

3. Copy environment file and generate the Laravel key.

```bash
cp .env.example .env
php artisan key:generate
```

4. Add Firebase values to `.env`, then place your service account JSON at:

```text
storage/app/firebase/chefvirtuo-service-account.json
```

5. Add approved lecturer emails:

```text
LECTURER_EMAILS=lecturer@example.edu,mylecturer@college.edu
```

6. Add the provided ChefVirtuo logo at:

```text
public/images/chefvirtuo-logo.png
```

7. Run the app.

```bash
npm run dev
php artisan serve
```

Firestore collection name:

```text
quizResults
```

Expected document structure:

```json
{
  "studentID": "TVET-2401",
  "module": "Knife Skills VR",
  "score": 18,
  "totalQuestions": 20,
  "percentage": 90,
  "timestamp": "Firestore timestamp"
}
```
