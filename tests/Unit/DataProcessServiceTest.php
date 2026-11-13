<?php

namespace Tests\Unit;

use App\Services\DataProcessService;
use PHPUnit\Framework\TestCase;

class DataProcessServiceTest extends TestCase
{
    public function test_pr_existing_with_belum_pr_is_partial(): void
    {
        $service = new DataProcessService();

        $this->assertSame('PR PARSIAL', $service->checkPrStatus('PR EKSISTING, BELUM PR'));
    }

    public function test_pr_baru_with_belum_pr_is_partial(): void
    {
        $service = new DataProcessService();

        $this->assertSame('PR PARSIAL', $service->checkPrStatus('PR BARU, BELUM PR'));
    }

    public function test_pr_without_waiting_status_is_complete(): void
    {
        $service = new DataProcessService();

        $this->assertSame('SUDAH PR', $service->checkPrStatus('PR EKSISTING'));
        $this->assertSame('SUDAH PR', $service->checkPrStatus('PR BARU, PR EKSISTING'));
    }

    public function test_stock_with_pr_is_classified_as_stock_and_pr(): void
    {
        $service = new DataProcessService();

        $this->assertSame('STOK DAN PR', $service->checkPrStatus('MENGGUNAKAN STOK, PR BARU'));
    }

    public function test_stock_only_is_classified_as_stock(): void
    {
        $service = new DataProcessService();

        $this->assertSame('STOK', $service->checkPrStatus('MENGGUNAKAN STOK'));
    }

    public function test_waiting_statuses_are_belum_pr(): void
    {
        $service = new DataProcessService();

        $this->assertSame('BELUM PR', $service->checkPrStatus('BELUM PR'));
        $this->assertSame('BELUM PR', $service->checkPrStatus('MENUNGGU EVALUASI PEMAKAIAN'));
        $this->assertSame('BELUM PR', $service->checkPrStatus(null));
    }

    public function test_unknown_status_is_reported(): void
    {
        $service = new DataProcessService();

        $this->assertSame('STATUS LAIN / TIDAK DIKENAL', $service->checkPrStatus('SESUATU YANG LAIN'));
    }
}
