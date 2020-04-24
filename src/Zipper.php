<?php

declare(strict_types=1);

namespace HTMLPurifier;

/**
 * A zipper is a purely-functional data structure which contains
 * a focus that can be efficiently manipulated.  It is known as
 * a "one-hole context".  This mutable variant implements a zipper
 * for a list as a pair of two arrays, laid out as follows:
 *
 *      Base list: 1 2 3 4 [ ] 6 7 8 9
 *      Front list: 1 2 3 4
 *      Back list: 9 8 7 6
 *
 * User is expected to keep track of the "current element" and properly
 * fill it back in as necessary.  (ToDo: Maybe it's more user friendly
 * to implicitly track the current element?)
 *
 * Nota bene: the current class gets confused if you try to store NULLs
 * in the list.
 */
class Zipper
{
    public $front;
    public $back;

    /**
     * HTMLPurifier\HTMLPurifier_Zipper constructor.
     *
     * @param $front
     * @param $back
     */
    public function __construct($front, $back)
    {
        $this->front = $front;
        $this->back = $back;
    }

    /**
     * Creates a zipper from an array, with a hole in the
     * 0-index position.
     *
     * @param array to zipper-ify.
     *
     * @return array|array{Zipper, mixed} of zipper and element of first position.
     */
    public static function fromArray(array $array): array
    {
        $z = new self([], array_reverse($array));
        $t = $z->delete(); // delete the "dummy hole"

        return [$z, $t];
    }

    /**
     * Convert zipper back into a normal array, optionally filling in
     * the hole with a value. (Usually you should supply a $t, unless you
     * are at the end of the array.)
     *
     * @param mixed|null $t
     */
    public function toArray($t = null)
    {
        $a = $this->front;
        if ($t !== null) {
            $a[] = $t;
        }

        for ($i = \count($this->back) - 1; $i >= 0; $i--) {
            $a[] = $this->back[$i];
        }

        return $a;
    }

    /**
     * Move hole to the next element.
     *
     * @param mixed $t Element to fill hole with
     *
     * @return mixed Original contents of new hole.
     */
    public function next($t)
    {
        if ($t !== null) {
            $this->front[] = $t;
        }

        return empty($this->back) ? null : array_pop($this->back);
    }

    /**
     * Iterated hole advancement.
     *
     * @param mixed $t Element to fill hole with
     * @param mixed $n How many forward to advance hole
     *
     * @return mixed Original contents of new hole, i away
     */
    public function advance($t, $n)
    {
        for ($i = 0; $i < $n; $i++) {
            $t = $this->next($t);
        }

        return $t;
    }

    /**
     * Move hole to the previous element
     *
     * @param mixed $t Element to fill hole with
     *
     * @return mixed Original contents of new hole.
     */
    public function prev($t)
    {
        if ($t !== null) {
            $this->back[] = $t;
        }

        return empty($this->front) ? null : array_pop($this->front);
    }

    /**
     * Delete contents of current hole, shifting hole to
     * next element.
     *
     * @return mixed Original contents of new hole.
     */
    public function delete()
    {
        return empty($this->back) ? null : array_pop($this->back);
    }

    /**
     * Returns true if we are at the end of the list.
     *
     * @return bool
     */
    public function done(): bool
    {
        return empty($this->back);
    }

    /**
     * Insert element before hole.
     *
     * @param mixed Element to insert
     */
    public function insertBefore($t): void
    {
        if ($t !== null) {
            $this->front[] = $t;
        }
    }

    /**
     * Insert element after hole.
     *
     * @param mixed Element to insert
     */
    public function insertAfter($t): void
    {
        if ($t !== null) {
            $this->back[] = $t;
        }
    }

    /**
     * Splice in multiple elements at hole.  Functional specification
     * in terms of array_splice:
     *
     *      $arr1 = $arr;
     *      $old1 = array_splice($arr1, $i, $delete, $replacement);
     *
     *      list($z, $t) = HTMLPurifier\HTMLPurifier_Zipper::fromArray($arr);
     *      $t = $z->advance($t, $i);
     *      list($old2, $t) = $z->splice($t, $delete, $replacement);
     *      $arr2 = $z->toArray($t);
     *
     *      assert($old1 === $old2);
     *      assert($arr1 === $arr2);
     *
     * NB: the absolute index location after this operation is
     * *unchanged!*
     *
     * @param $t
     * @param $delete
     * @param $replacement
     *
     * @return array
     */
    public function splice($t, $delete, $replacement): array
    {
        // delete
        $old = [];
        $r = $t;
        for ($i = $delete; $i > 0; $i--) {
            $old[] = $r;
            $r = $this->delete();
        }

        // insert
        for ($i = \count($replacement) - 1; $i >= 0; $i--) {
            $this->insertAfter($r);
            $r = $replacement[$i];
        }

        return [$old, $r];
    }
}
