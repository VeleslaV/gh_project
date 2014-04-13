<?

namespace VelJo\GHProjectBundle\ParserListener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;


class VelJoParserListener
{
    public function onKernelException(GetResponseForExceptionEvent $event)
    {

    }
}