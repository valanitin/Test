<?php
/**
 * @author      LuxuryUnlimited
 * @copyright   Copyright © 2022. All rights reserved.
 */
declare(strict_types=1);

namespace LuxuryUnlimited\Mytickets\Api;

interface TicketInterface
{
    /**
     * Get Ticket Data
     *
     * @api
     * @return string[]
     */
    public function getTicket();

    /**
     * Update Ticket Data
     *
     * @api
     * @return string[]
     */
    public function updateTicket();

    /**
     * Get customer Ticket Data
     *
     * @api
     * @return string[]
     */
    public function getMyTicket();

    /**
     * @api
     * @return mixed
     */
    public function getMyTicketByEmail();
}
