# Structural Design Patterns — IPT Lab

Riejed Aniko L. Macatula
BSIT 3A

**Course:** Integrative Programming and Technologies
**Lab:** Structural Design Patterns in Web Application Development (Adapter, Bridge, Composite, Decorator)

## Contents

| Folder | Pattern | Run |
|---|---|---|
| [lab1_adapter/](lab1_adapter/) | Adapter — incompatible CSV/JSON/text writers | `php without_web.php` then `php with_pattern.php` |
| [lab2_bridge/](lab2_bridge/) | Bridge — TimesFormatter × DataSource × Compressor | `php without_web.php` then `php with_pattern.php` |
| [lab3_composite/](lab3_composite/) | Composite — Forum Thread/Post + RAG Document tree | `php without_web.php` then `php with_pattern.php` |
| [lab4_decorator/](lab4_decorator/) | Decorator — HTTP middleware pipeline | `php without_web.php` then `php with_pattern.php` |

Each folder has:
- `without_web.php` — BAD version (runnable, shows the smell)
- `with_pattern.php` — GOOD version (runnable, TODOs complete)
- `labN_uml.png` / `labN_uml.puml` — UML diagram (PlantUML source included)
- `reflection.pdf` — 1-page reflection: smell, fix, Gamma citation

## Status

All labs complete. Extensibility proven per lab:

- **Lab 1**: `TextFileAdapter::write()` and `ReportExporter::export()` implemented; added a 4th adapter (`XmlWriterAdapter` wrapping `XmlLibrary`) with zero changes to `ReportExporter`.
- **Lab 2**: `TimesFormatter::setCompressor()` and `TimesheetJsonFormatter` implemented; demoed 2 data sources × 2 compressors.
- **Lab 3**: `Thread::display()` loop fixed; RAG `Document`/`Section`/`Chunk` variant added; `Bundle` leaf added as a new `ForumComponent` with no client changes.
- **Lab 4**: `RetryDecorator::get()` and `TokenCounterDecorator::get()` implemented; demoed how decorator wrapping order changes behavior.
