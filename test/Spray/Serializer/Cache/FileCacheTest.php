<?php

namespace Spray\Serializer\Cache;

use PHPUnit_Framework_TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class FileCacheTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Filesystem
     */
    private $filesystem;
    
    /**
     * @var Finder
     */
    private $finder;
    
    protected function setUp()
    {
        $this->filesystem = $this->getMockBuilder('Symfony\Component\Filesystem\Filesystem')
            ->disableOriginalConstructor()
            ->getMock();
        $this->finder = $this->getMockBuilder('Symfony\Component\Finder\Finder')
            ->disableOriginalConstructor()
            ->getMock();
    }
    
    protected function createCache()
    {
        return new FileCache($this->filesystem, '/tmp');
    }
    
    public function testDoesNotExist()
    {
        $this->filesystem->expects($this->once())
            ->method('exists')
            ->with($this->equalTo('/tmp/Foo_BarSerializer.php'))
            ->will($this->returnValue(false));
        $this->assertFalse($this->createCache()->exists('Foo\Bar'));
    }
    
    public function testDoesExist()
    {
        $this->filesystem->expects($this->once())
            ->method('exists')
            ->with($this->equalTo('/tmp/Foo_BarSerializer.php'))
            ->will($this->returnValue(true));
        $this->assertTrue($this->createCache()->exists('Foo\Bar'));
    }
    
    public function testSaveSubjectResult()
    {
        $this->filesystem->expects($this->once())
            ->method('dumpFile')
            ->with(
                $this->equalTo('/tmp/Foo_BarSerializer.php'),
                $this->equalTo('foobar'));
        $this->createCache()->save('Foo_Bar', 'foobar');
    }
    
    public function testLoadSavedObject()
    {
        $className = uniqid('Tmp');
        $this->filesystem->expects($this->once())
            ->method('exists')
            ->with($this->equalTo('/tmp/' . $className . 'Serializer.php'))
            ->will($this->returnValue(true));
        file_put_contents('/tmp/' . $className . 'Serializer.php', '<?php class ' . $className . 'Serializer {}');
        $serializer = $this->createCache()->load($className);
        $this->assertInstanceOf(
            $className. 'Serializer',
            $serializer
        );
        unlink('/tmp/' . $className . 'Serializer.php');
    }
}
