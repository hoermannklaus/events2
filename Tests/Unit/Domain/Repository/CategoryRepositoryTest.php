<?php

namespace JWeiland\Events2\Tests\Unit\Domain\Repository;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */
use JWeiland\Events2\Domain\Repository\CategoryRepository;
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Extbase\Persistence\Generic\Query;

/**
 * Test case.
 *
 * @author Stefan Froemken <projects@jweiland.net>
 */
class CategoryRepositoryTest extends UnitTestCase
{
    /**
     * @var \JWeiland\Events2\Domain\Repository\CategoryRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $subject;

    /**
     * set up.
     */
    public function setUp()
    {
        $this->subject = $this
            ->getMockBuilder(CategoryRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['createQuery'])
            ->getMock();
    }

    /**
     * tear down.
     */
    public function tearDown()
    {
        unset($this->subject);
    }

    /**
     * @test
     */
    public function getSelectedCategoriesConvertsWrongCategoriesToInteger()
    {
        /** @var Query|\PHPUnit_Framework_MockObject_MockObject $query */
        $query = $this
            ->getMockBuilder(Query::class)
            ->disableOriginalConstructor()
            ->getMock();
        $query->expects($this->once())->method('matching')->willReturn($query);
        $query->expects($this->once())->method('in')->with(
            $this->equalTo('uid'),
            $this->equalTo([1, 2, 4])
        );

        $this->subject->expects($this->once())->method('createQuery')->willReturn($query);

        $this->subject->getSelectedCategories('1,2test,drei,4');
    }

    /**
     * @test
     */
    public function getSelectedCategoriesWithNonParentWillNotCallEquals()
    {
        /** @var Query|\PHPUnit_Framework_MockObject_MockObject $query */
        $query = $this
            ->getMockBuilder(Query::class)
            ->disableOriginalConstructor()
            ->getMock();
        $query->expects($this->never())->method('equals');
        $query->expects($this->once())->method('matching')->willReturn($query);

        $this->subject->expects($this->once())->method('createQuery')->willReturn($query);

        $this->subject->getSelectedCategories('1,2,3,4');
    }

    /**
     * @test
     */
    public function getSelectedCategoriesWithGivenParentWillCallEquals()
    {
        /** @var Query|\PHPUnit_Framework_MockObject_MockObject $query */
        $query = $this
            ->getMockBuilder(Query::class)
            ->disableOriginalConstructor()
            ->getMock();
        $query->expects($this->once())->method('equals')->with(
            $this->equalTo('parent'),
            $this->equalTo(5)
        );
        $query->expects($this->once())->method('matching')->willReturn($query);

        $this->subject->expects($this->once())->method('createQuery')->willReturn($query);

        // parent (5) should be casted to integer
        $this->subject->getSelectedCategories('1,2,3,4', '5');
    }
}
