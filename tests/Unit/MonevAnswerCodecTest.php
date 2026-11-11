<?php

namespace Tests\Unit;

use App\Services\MonevAnswerCodec;
use Tests\TestCase;

class MonevAnswerCodecTest extends TestCase
{
    public function test_numeric_answer_is_stored_in_numeric_value(): void
    {
        $result = (new MonevAnswerCodec())->encode('integer', 12);

        $this->assertSame(12, $result['numeric_value']);
        $this->assertNull($result['text_value']);
    }

    public function test_multi_select_answer_is_json_in_text_value(): void
    {
        $result = (new MonevAnswerCodec())->encode('select', ['Tatap muka', 'Daring']);

        $this->assertNull($result['numeric_value']);
        $this->assertSame('["Tatap muka","Daring"]', $result['text_value']);
    }
}
