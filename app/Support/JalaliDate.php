<?php

namespace App\Support;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use InvalidArgumentException;

class JalaliDate
{
    /**
     * Format a stored UTC date for display in the Tehran timezone and Jalali calendar.
     */
    public static function dateTime(?CarbonInterface $date): string
    {
        if ($date === null) {
            return 'تعیین نشده';
        }

        $tehranDate = $date->copy()->setTimezone('Asia/Tehran');
        [$year, $month, $day] = self::fromGregorian(
            $tehranDate->year,
            $tehranDate->month,
            $tehranDate->day,
        );

        return self::toPersianDigits(sprintf(
            '%04d/%02d/%02d، %02d:%02d',
            $year,
            $month,
            $day,
            $tehranDate->hour,
            $tehranDate->minute,
        ));
    }

    /**
     * Convert a Jalali date-time in Tehran time to UTC for storage.
     *
     * @throws InvalidArgumentException
     */
    public static function parseForStorage(string $value): CarbonImmutable
    {
        $normalized = self::toLatinDigits(trim($value));

        if (preg_match('/^(\d{4})\/(\d{1,2})\/(\d{1,2})\s+([01]?\d|2[0-3]):([0-5]\d)$/', $normalized, $matches) !== 1) {
            throw new InvalidArgumentException('تاریخ را با الگوی ۱۴۰۵/۰۷/۱۵ ۰۹:۳۰ وارد کنید.');
        }

        $jalaliYear = (int) $matches[1];
        $jalaliMonth = (int) $matches[2];
        $jalaliDay = (int) $matches[3];
        [$gregorianYear, $gregorianMonth, $gregorianDay] = self::toGregorian($jalaliYear, $jalaliMonth, $jalaliDay);

        $date = CarbonImmutable::createSafe(
            $gregorianYear,
            $gregorianMonth,
            $gregorianDay,
            (int) $matches[4],
            (int) $matches[5],
            0,
            'Asia/Tehran',
        );

        if ($date === null) {
            throw new InvalidArgumentException('تاریخ واردشده معتبر نیست.');
        }

        [$verifiedYear, $verifiedMonth, $verifiedDay] = self::fromGregorian($date->year, $date->month, $date->day);

        if ([$verifiedYear, $verifiedMonth, $verifiedDay] !== [$jalaliYear, $jalaliMonth, $jalaliDay]) {
            throw new InvalidArgumentException('تاریخ واردشده در تقویم شمسی معتبر نیست.');
        }

        return $date->utc();
    }

    /**
     * Convert a Gregorian date to its Jalali year, month, and day.
     *
     * @return array{int, int, int}
     */
    private static function fromGregorian(int $gregorianYear, int $gregorianMonth, int $gregorianDay): array
    {
        $daysBeforeMonth = [0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334];

        if ($gregorianYear > 1600) {
            $jalaliYear = 979;
            $gregorianYear -= 1600;
        } else {
            $jalaliYear = 0;
            $gregorianYear -= 621;
        }

        $nextGregorianYear = $gregorianMonth > 2 ? $gregorianYear + 1 : $gregorianYear;
        $days = (365 * $gregorianYear)
            + intdiv($nextGregorianYear + 3, 4)
            - intdiv($nextGregorianYear + 99, 100)
            + intdiv($nextGregorianYear + 399, 400)
            - 80
            + $gregorianDay
            + $daysBeforeMonth[$gregorianMonth - 1];

        $jalaliYear += 33 * intdiv($days, 12053);
        $days %= 12053;
        $jalaliYear += 4 * intdiv($days, 1461);
        $days %= 1461;

        if ($days > 365) {
            $jalaliYear += intdiv($days - 1, 365);
            $days = ($days - 1) % 365;
        }

        if ($days < 186) {
            $jalaliMonth = 1 + intdiv($days, 31);
            $jalaliDay = 1 + ($days % 31);
        } else {
            $jalaliMonth = 7 + intdiv($days - 186, 30);
            $jalaliDay = 1 + (($days - 186) % 30);
        }

        return [$jalaliYear, $jalaliMonth, $jalaliDay];
    }

    /**
     * Convert a Jalali date to its Gregorian year, month, and day.
     *
     * @return array{int, int, int}
     */
    private static function toGregorian(int $jalaliYear, int $jalaliMonth, int $jalaliDay): array
    {
        $year = $jalaliYear + 1595;
        $days = -355668
            + (365 * $year)
            + (intdiv($year, 33) * 8)
            + intdiv(($year % 33) + 3, 4)
            + $jalaliDay
            + ($jalaliMonth < 7 ? ($jalaliMonth - 1) * 31 : (($jalaliMonth - 7) * 30) + 186);
        $gregorianYear = 400 * intdiv($days, 146097);
        $days %= 146097;

        if ($days > 36524) {
            $gregorianYear += 100 * intdiv(--$days, 36524);
            $days %= 36524;

            if ($days >= 365) {
                $days++;
            }
        }

        $gregorianYear += 4 * intdiv($days, 1461);
        $days %= 1461;

        if ($days > 365) {
            $gregorianYear += intdiv($days - 1, 365);
            $days = ($days - 1) % 365;
        }

        $dayOfYear = $days + 1;
        $daysInMonth = [31, self::isGregorianLeapYear($gregorianYear) ? 29 : 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        $gregorianMonth = 1;

        foreach ($daysInMonth as $daysInCurrentMonth) {
            if ($dayOfYear <= $daysInCurrentMonth) {
                break;
            }

            $dayOfYear -= $daysInCurrentMonth;
            $gregorianMonth++;
        }

        return [$gregorianYear, $gregorianMonth, $dayOfYear];
    }

    /**
     * Determine whether a Gregorian year is a leap year.
     */
    private static function isGregorianLeapYear(int $year): bool
    {
        return ($year % 4 === 0 && $year % 100 !== 0) || $year % 400 === 0;
    }

    /**
     * Convert Persian and Arabic-Indic digits to Latin digits.
     */
    private static function toLatinDigits(string $value): string
    {
        return strtr($value, [
            '۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4',
            '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9',
            '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4',
            '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9',
        ]);
    }

    /**
     * Convert Latin digits to Persian digits.
     */
    private static function toPersianDigits(string $value): string
    {
        return strtr($value, [
            '0' => '۰',
            '1' => '۱',
            '2' => '۲',
            '3' => '۳',
            '4' => '۴',
            '5' => '۵',
            '6' => '۶',
            '7' => '۷',
            '8' => '۸',
            '9' => '۹',
        ]);
    }
}
