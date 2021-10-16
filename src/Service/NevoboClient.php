<?php

namespace Punch\NevoboBundle\Service;

use Exception;
use Punch\NevoboBundle\Model\PouleIndeling;
use Punch\NevoboBundle\Model\Sporthal;
use Punch\NevoboBundle\Model\Team;
use Punch\NevoboBundle\Model\Vereniging;
use Punch\NevoboBundle\Model\Wedstrijd;
use Symfony\Component\Serializer\Serializer;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class NevoboClient
{
    private CacheInterface $cache;
    private HttpClientInterface $client;
    private Serializer $serializer;
    private int $cacheDuration;
    private string $nevobo_url = 'https://api.nevobo.nl/v1';

    public function __construct(CacheInterface $cache, HttpClientInterface $client, Serializer $serializer, int $cacheDuration = 0)
    {
        $this->cache = $cache;
        $this->client = $client;
        $this->serializer = $serializer;
        $this->cacheDuration = $cacheDuration;
    }

    /**
     * @return PouleIndeling[]
     */
    public function getPouleIndelingen(string $poule): array
    {
        $endpoint = "competitie/pouleindelingen/?poule={$poule}";

        return $this->request($endpoint, PouleIndeling::class.'[]', true);
    }

    public function getVereniging(string $verenigingId): Vereniging
    {
        $endpoint = "relatie/verenigingen/$verenigingId";

        return $this->request($endpoint, Vereniging::class, false);
    }

    /**
     * @return Sporthal[]
     */
    public function getSporthallenForVereniging(string $verenigingId): array
    {
        $endpoint = "competitie/sporthallen?vereniging=$verenigingId";

        return $this->request($endpoint, Sporthal::class.'[]', true);
    }

    public function getTeam(string $teamId): Team
    {
        $endpoint = "competitie/teams/$teamId";

        return $this->request($endpoint, Team::class, false);
    }

    /**
     * @return Team[]
     */
    public function getTeamsForVereniging(string $verenigingId): array
    {
        $endpoint = "competitie/teams/?vereniging=$verenigingId";

        return $this->request($endpoint, Team::class.'[]', true);
    }

    /**
     * @return Wedstrijd[]
     */
    public function getWedstrijdenForPoule(string $pouleId): array
    {
        $endpoint = "competitie/wedstrijden?poule=$pouleId";

        return $this->request($endpoint, Wedstrijd::class.'[]', true, limit: 1000);
    }

    /**
     * @return Wedstrijd[]
     */
    public function getWedstrijdenForVereniging(string $verenigingId): array
    {
        $endpoint = "competitie/wedstrijden?vereniging=$verenigingId";

        return $this->request($endpoint, Wedstrijd::class.'[]', true, limit: 1000);
    }

    /**
     * @return Wedstrijd[]
     */
    public function getWedstrijdProgrammaForVereniging(string $verenigingId): array
    {
        $endpoint = "competitie/wedstrijden/programma?vereniging=$verenigingId";

        return $this->request($endpoint, Wedstrijd::class.'[]', true, limit: 1000);
    }

    /**
     * @return Wedstrijd[]
     */
    public function getWedstrijdResultaatForVereniging(string $verenigingId): array
    {
        $endpoint = "competitie/wedstrijden/resultaat?vereniging=$verenigingId";

        return $this->request($endpoint, Wedstrijd::class.'[]', true, limit: 1000);
    }

    private function request(string $endpoint, string $fqcn, bool $paginated = false, $limit = 50)
    {
        $url = "{$this->nevobo_url}/$endpoint";

        $cacheKey = str_replace('/', '', $endpoint);

        if ($this->cacheDuration) {
            return $this->cache->get($cacheKey, function (ItemInterface $item) use ($url, $paginated, $fqcn, $limit) {
                $item->expiresAfter($this->cacheDuration);
                return $this->_request($url, $paginated, $fqcn, $limit);
            });
        } else {
            return $this->_request($url, $paginated, $fqcn, $limit);
        }
    }

    private function _request(string $url, bool $paginated, string $fqcn, $limit)
    {
        $response = $this->client->request(
            'GET',
            $url, [
                'query' => [
                    'page' => 1,
                    'limit' => $limit,
                ],
            ]
        );

        $statusCode = $response->getStatusCode();
        if ('200' != $statusCode) {
            $response = $response->getContent(false);
            throw new Exception("Endpoint $url returned a response of $statusCode\n{$response}");
        }

        $content = $response->getContent();

        // Get rid of their pagination
        $decoded = json_decode($content, true);
        $decoded = $paginated ? $decoded['_embedded']['items'] : $decoded;
        $encoded = json_encode($decoded);

        return $this->serializer->deserialize($encoded, $fqcn, 'json');
    }
}
