<?php

namespace Tests\Unit;

use App\Support\AdminChatbotIntentParser;
use PHPUnit\Framework\TestCase;

class AdminChatbotIntentParserTest extends TestCase
{
    public function test_it_parses_sales_comparison_with_explicit_periods(): void
    {
        $parser = new AdminChatbotIntentParser();

        $parsed = $parser->parse('penjualan minggu ini dibanding minggu lalu');

        $this->assertSame('perbandingan_penjualan', $parsed['intent']);
        $this->assertSame('current_week', $parsed['parameters']['period']);
        $this->assertSame('previous_week', $parsed['parameters']['compare_period']);
    }

    public function test_it_parses_custom_day_range_for_sales_summary(): void
    {
        $parser = new AdminChatbotIntentParser();

        $parsed = $parser->parse('ringkasan penjualan tanggal 1-15');

        $this->assertSame('ringkasan_penjualan', $parsed['intent']);
        $this->assertSame('custom_day_range', $parsed['parameters']['period']);
        $this->assertSame(1, $parsed['parameters']['day_from']);
        $this->assertSame(15, $parsed['parameters']['day_to']);
    }

    public function test_it_parses_dead_stock_days(): void
    {
        $parser = new AdminChatbotIntentParser();

        $parsed = $parser->parse('produk tidak terjual 45 hari');

        $this->assertSame('stok_mati', $parsed['intent']);
        $this->assertSame(45, $parsed['parameters']['days']);
    }

    public function test_it_parses_follow_up_stock_history_phrase(): void
    {
        $parser = new AdminChatbotIntentParser();

        $parsed = $parser->parse('riwayatnya gimana');

        $this->assertSame('riwayat_stock_movement', $parsed['intent']);
    }

    public function test_it_tolerates_simple_typos_for_low_stock_queries(): void
    {
        $parser = new AdminChatbotIntentParser();

        $parsed = $parser->parse('produk stok menpis');

        $this->assertSame('produk_low_stock', $parsed['intent']);
    }
}
