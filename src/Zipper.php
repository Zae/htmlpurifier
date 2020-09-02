<?php

declare(strict_types=1);

namespace HTMLPurifier;

use function count;

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
 *
 */
class Zipper
{
    public $front;
    public $back;

    /**
     * HTMLPurifier\HTMLPurifier_Zipper constructor.
     *
     * @param array $front
     * @param array $back
     */
    final public function __construct(array $front, array $back)
    {
        $this->front = $front;
        $this->back = $back;
    }

    /**
     * Creates a zipper from an array, with a hole in the
     * 0-index position.
     *
     * @template T
     * @param array $array to zipper-ify.
     * @psalm-param T[] $array
     * @phpstan-param T[] $array
     *
     * @return array of zipper and element of first position.
     * @phpstan-return array{Zipper, T|null}
     * @psalm-return array{Zipper, T|null}
     */
    public static function fromArray(array $array): array
    {
        $z = new static([], array_reverse($array));
        $t = $z->delete(); // delete the "dummy hole"

        return [$z, $t];
    }

    /**
     * Convert zipper back into a normal array, optionally filling in
     * the hole with a value. (Usually you should supply a $t, unless you
     * are at the end of the array.)
     *
     * @template T
     * @param T|null $t
     *
     * @return array
     */
    public function toArray($t = null): array
    {
        $a = $this->front;
        if ($t !== null) {
            $a[] = $t;
        }

        for ($i = count($this->back) - 1; $i >= 0; $i--) {
            $a[] = $this->back[$i];
        }

        return $a;
    }

    /**
     * Move hole to the next element.
     *
     * @template T
     * @param T|null $t Element to fill hole with
     *
     * @return T Original contents of new hole.
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
     * @template T
     * @param T|null $t Element to fill hole with
     * @param int $n How many forward to advance hole
     *
     * @return T Original contents of new hole, i away
     */
    public function advance($t, int $n)
    {
        for ($i = 0; $i < $n; $i++) {
            $t = $this->next($t);
        }

        return $t;
    }

    /**
     * Move hole to the previous element
     *
     * @template T
     * @param T|null $t Element to fill hole with
     *
     * @return T Original contents of new hole.
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
     * @template T
     * @return T|null Original contents of new hole.
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
     * @template T
     * @param T|null $t Element to insert
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
     * @template T
     * @param T|null $t Element to insert
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
     * @template T
     * @param T     $t
     * @param int       $delete
     * @param array|T[]     $replacement
     *
     * @return array|array{Zipper, T}
     */
    public function splice($t, int $delete, array $replacement): array
    {
        // delete
        $old = [];
        $r = $t;
        for ($i = $delete; $i > 0; $i--) {
            $old[] = $r;
            $r = $this->delete();
        }

        // insert
        for ($i = count($replacement) - 1; $i >= 0; $i--) {
            $this->insertAfter($r);
            $r = $replacement[$i];
        }

        return [$old, $r];
    }
}
