<?php
// Lab 3 - WITH Composite (GOOD starter, live API, has TODOs)
interface ForumComponent {
    public function display(int $depth = 0): void;
}
class Post implements ForumComponent {
    public function __construct(private string $author, private string $message) {}
    public function display(int $depth = 0): void {
        $indent = str_repeat("  ", $depth);
        echo $indent . "- Post by {$this->author}: {$this->message}\n";
    }
}
class Thread implements ForumComponent {
    /** @var ForumComponent[] */
    private array $children = [];
    public function __construct(private string $title) {}
    public function add(ForumComponent $c): void { $this->children[] = $c; }
    public function display(int $depth = 0): void {
        echo str_repeat("  ", $depth) . "+ Thread: {$this->title}\n";
        foreach ($this->children as $child) {
            $child->display($depth + 1);
        }
    }
    public static function fromApi(int $postId): self {
        $postJson = file_get_contents("https://jsonplaceholder.typicode.com/posts/$postId");
        $post = json_decode($postJson, true);
        $thread = new self($post["title"]);
        $thread->add(new Post("Author {$post['userId']}", substr($post["body"], 0, 40) . "..."));
        $commentsJson = file_get_contents("https://jsonplaceholder.typicode.com/posts/$postId/comments");
        $comments = json_decode($commentsJson, true);
        $replies = new Thread("Replies");
        foreach (array_slice($comments, 0, 2) as $c) $replies->add(new Post($c["email"], substr($c["body"], 0, 30) . "..."));
        $thread->add($replies);
        return $thread;
    }
}
// A pre-set combo leaf: renders as one Post-like line, no children of its own.
class Bundle implements ForumComponent {
    public function __construct(private string $label) {}
    public function display(int $depth = 0): void {
        echo str_repeat("  ", $depth) . "- Bundle: {$this->label}\n";
    }
}

// ---------- RAG variant: same shape, renamed for text retrieval ----------
interface TextComponent {
    public function getText(): string;
    public function embed(): void;
}

class Chunk implements TextComponent {
    public function __construct(private string $text) {}
    public function getText(): string {
        return $this->text;
    }
    public function embed(): void {
        echo "  [embed] chunk (" . strlen($this->text) . " chars)\n";
    }
}

class Section implements TextComponent {
    /** @var TextComponent[] */
    private array $children = [];
    public function __construct(private string $title) {}
    public function add(TextComponent $c): void { $this->children[] = $c; }
    public function getText(): string {
        $parts = [$this->title];
        foreach ($this->children as $child) {
            $parts[] = $child->getText();
        }
        return implode("\n", $parts);
    }
    public function embed(): void {
        foreach ($this->children as $child) {
            $child->embed();
        }
    }
}

class Document implements TextComponent {
    /** @var TextComponent[] */
    private array $children = [];
    public function __construct(private string $title) {}
    public function add(TextComponent $c): void { $this->children[] = $c; }
    public function getText(): string {
        $parts = [$this->title];
        foreach ($this->children as $child) {
            $parts[] = $child->getText();
        }
        return implode("\n", $parts);
    }
    public function embed(): void {
        foreach ($this->children as $child) {
            $child->embed();
        }
    }
}

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    echo "WITH Composite (GOOD - complete TODOs):\n";
    $thread = Thread::fromApi(1); // live to jsonplaceholder.typicode.com
    $thread->add(new Bundle("Welcome pack (rules + FAQ)"));
    $thread->display();

    echo "\nRAG variant:\n";
    $doc = new Document("Attendance Policy");
    $section = new Section("Section 1: Late Arrivals");
    $section->add(new Chunk("Employees must log in within 15 minutes of shift start."));
    $section->add(new Chunk("Repeated lateness is escalated to HR."));
    $doc->add($section);
    echo $doc->getText() . "\n";
    $doc->embed();
}
