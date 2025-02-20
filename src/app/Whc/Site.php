<?php

declare(strict_types=1);

namespace App\Whc;

class Site
{
    public int $idNo;
    public int $revBis;
    public string $nameEn;
    public string $shortDescriptionEn;
    public string $justificationEn;
    public int $dateInscribed;
    public string $secondaryDates;
    public int $danger;
    public string $dateEnd;
    public string $dangerList;
    public float $longitude;
    public float $latitude;
    public float $areaHectares;
    public int $c1;
    public int $c2;
    public int $c3;
    public int $c4;
    public int $c5;
    public int $c6;
    public int $n7;
    public int $n8;
    public int $n9;
    public int $n10;
    public string $criteriaTxt;
    public string $category;
    public string $categoryShort;
    public string $statesNameEn;
    public string $regionEn;
    public string $isoCode;
    public string $udnpCode;
    public bool $transboundary;

    /**
     * @param array<string, mixed> $row
     */
    public static function fromRow(array $row): self
    {
        $site = new self();
        $site->idNo = (int) $row['id_no'];
        $site->revBis = (int) $row['rev_bis'];
        $site->nameEn = $row['name_en'];
        $site->shortDescriptionEn = $row['short_description_en'];
        $site->justificationEn = $row['justification_en'];
        $site->dateInscribed = (int)$row['date_inscribed'];
        $site->secondaryDates = $row['secondary_dates'];
        $site->danger = (int)$row['danger'];
        $site->dateEnd = $row['date_end'];
        $site->dangerList = $row['danger_list'];
        $site->longitude = (float) $row['longitude'];
        $site->latitude = (float) $row['latitude'];
        $site->areaHectares = (float) $row['area_hectares'];
        $site->c1 = (int) $row['C1'];
        $site->c2 = (int) $row['C2'];
        $site->c3 = (int) $row['C3'];
        $site->c4 = (int) $row['C4'];
        $site->c5 = (int) $row['C5'];
        $site->c6 = (int) $row['C6'];
        $site->n7 = (int) $row['N7'];
        $site->n8 = (int) $row['N8'];
        $site->n9 = (int) $row['N9'];
        $site->n10 = (int) $row['N10'];
        $site->criteriaTxt = $row['criteria_txt'];
        $site->category = $row['category'];
        $site->categoryShort = $row['category_short'];
        $site->statesNameEn = $row['states_name_en'];
        $site->regionEn = $row['region_en'];
        $site->isoCode = $row['iso_code'];
        $site->udnpCode = $row['udnp_code'];
        $site->transboundary = (bool) $row['transboundary'];

        return $site;
    }
}
