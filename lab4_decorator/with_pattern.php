<?php
// Lab 4 - WITH Decorator (GOOD starter, live API, has TODOs)
interface HttpClient {
    public function get(string $url): string;
}
class BaseHttpClient implements HttpClient {
    public function get(string $url): string {
        // Live to real endpoint
        return file_get_contents($url);
    }
}
abstract class HttpDecorator implements HttpClient {
    public function __construct(protected HttpClient $wrapped) {}
}
class LoggingDecorator extends HttpDecorator {
    public function get(string $url): string {
        echo "[Log] GET $url\n";
        $r = $this->wrapped->get($url);
        echo "[Log] Got " . strlen($r) . " bytes\n";
        return $r;
    }
}
class CachingDecorator extends HttpDecorator {
    private array $cache = [];
    public function get(string $url): string {
        if (isset($this->cache[$url])) { echo "[Cache] Hit $url\n"; return $this->cache[$url]; }
        $r = $this->wrapped->get($url);
        $this->cache[$url] = $r;
        echo "[Cache] Stored $url\n";
        return $r;
    }
}
// TODO: Implement RetryDecorator (3 tries, catch Exception)
class RetryDecorator extends HttpDecorator {
    public function get(string $url): string {
        for ($i = 0; $i < 3; $i++) {
            try {
                return $this->wrapped->get($url);
            } catch (Exception $e) {
                if ($i === 2) {
                    throw $e;
                }
            }
        }
    }
}
class TokenCounterDecorator extends HttpDecorator {
    public function get(string $url): string {
        $r = $this->wrapped->get($url);
        echo "[Tokens] " . str_word_count($r) . "\n";
        return $r;
    }
}
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    echo "WITH Decorator (GOOD - complete TODOs):\n";
    $url = "https://jsonplaceholder.typicode.com/posts/1";

    echo "-- Caching(Logging(Base)): cache hit skips the log lines --\n";
    $client = new CachingDecorator(new LoggingDecorator(new BaseHttpClient()));
    echo substr($client->get($url), 0, 60) . "...\n"; // live, logged
    echo substr($client->get($url), 0, 60) . "...\n"; // cache hit, no log

    echo "\n-- Logging(Caching(Base)): log lines print even on a cache hit --\n";
    $client2 = new LoggingDecorator(new CachingDecorator(new BaseHttpClient()));
    echo substr($client2->get($url), 0, 60) . "...\n"; // live, logged
    echo substr($client2->get($url), 0, 60) . "...\n"; // logged, cache hit happens inside

    echo "\n-- Retry + TokenCounter stacked on top --\n";
    $full = new RetryDecorator(new TokenCounterDecorator(new CachingDecorator(new LoggingDecorator(new BaseHttpClient()))));
    echo substr($full->get($url), 0, 60) . "...\n";
}
