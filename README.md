# ToDo Liste API #

## Installation ##

1. Im Root-Verzeichnis ausführen: 
    ```
    $ composer install

    $ php bin/console server:run
    ```

2. Im Browser Frontend aufrufen (vermutlich mit localhost:4200) oder über HTTP-Tool 
auf GET localhost:8000/tools zugreifen.


## Routen ##
* `GET /tools`: Liefert alle vorhandenen ToDo Einträge aus der DB 
* `GET /tools/:id`: Liefert ein spezifische ToDo aus der DB  
* `POST /tools`: Erzeugt legt ein neues ToDo in der DB an  
* `PATCH /tools/:id`: Ändert ein ToDo  
* `DELETE /tools/:id`: Löscht ein Todo


## Devnotes ##

### Enwicklung Arbeitsschritte ###

* Installation Symfony/skeleton
* Git support aktiviert
* Annotation und Doctrine:ORM via sqlite eingebunden
* TodoController angelegt
* Datenbank erzeugt
* ExceptionEventListener implementiert
