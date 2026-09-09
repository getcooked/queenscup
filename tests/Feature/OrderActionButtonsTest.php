<?php

namespace Tests\Feature;

use App\Models\Reservation;
use Tests\TestCase;

/**
 * The staff order actions.
 *
 * Two faults made these dead on any order that lives on the server:
 *
 *  - the id was written into the onclick unquoted, so a reference like
 *    QC-8F2K4D became updateOrderStatus(QC-8F2K4D,...) and the browser threw
 *    a ReferenceError looking for a variable named QC;
 *  - the button after "preparing" sent 'serving', a status the server has
 *    never had, so it was refused and the order stranded there.
 */
class OrderActionButtonsTest extends TestCase
{
    private function ordersPage(): string
    {
        return $this->get('/orders')->assertOk()->getContent();
    }

    public function test_order_ids_are_quoted_in_the_action_handlers()
    {
        $html = $this->ordersPage();

        // "('+o.id+'" writes the raw id straight into the attribute.
        $this->assertStringNotContainsString(
            "('+o.id+'",
            $html,
            'An order id is written into an onclick unquoted, which throws a ReferenceError for reference ids.'
        );

        // The quoted form the buttons should use instead.
        $this->assertStringContainsString("('+oid+'", $html);
        $this->assertStringContainsString('var oid=', $html);
    }

    public function test_orders_are_matched_by_string_so_a_quoted_id_still_resolves()
    {
        $html = $this->ordersPage();

        $this->assertStringNotContainsString('return or.id===id;', $html);
        $this->assertStringContainsString('return String(or.id)===String(id);', $html);
    }

    public function test_the_status_after_preparing_is_one_the_server_accepts()
    {
        $html = $this->ordersPage();

        // A server order must be advanced to 'ready'; only a local record
        // uses the older 'serving' step.
        $this->assertStringContainsString("o.serverId?'ready':'serving'", $html);

        $this->assertContains(
            'ready',
            Reservation::ALLOWED_TRANSITIONS[Reservation::STATUS_PREPARING],
            'The page advances a preparing order to ready, so the server must allow it.'
        );

        $this->assertNotContains(
            'serving',
            Reservation::STATUSES,
            "'serving' is not a server status, so no button may send it to the server."
        );
    }

    public function test_a_ready_or_serving_order_can_be_completed()
    {
        // Previously only 'ready' offered Complete, so a local order sitting
        // at 'serving' had no way to finish.
        $this->assertStringContainsString(
            "o.status==='ready'||o.status==='serving'",
            $this->ordersPage()
        );
    }

    public function test_a_preparing_order_can_still_be_cancelled()
    {
        $this->assertStringContainsString(
            "o.status==='pending'||o.status==='preparing'",
            $this->ordersPage()
        );

        $this->assertContains(
            'cancelled',
            Reservation::ALLOWED_TRANSITIONS[Reservation::STATUS_PREPARING]
        );
    }
}
