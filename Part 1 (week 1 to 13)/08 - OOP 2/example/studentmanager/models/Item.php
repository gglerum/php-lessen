<?php
interface Item
{
    public function getId();
    public function toArray();
    public function getOverviewText();
}
