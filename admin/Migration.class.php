<?php

/**
 * ThWboard - PHP/MySQL Bulletin Board System
 * ==========================================
 *
 * Copyright (C) 2015 by Marcel Metz
 *
 * This file is part of ThWboard
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program;  If not, see <http://www.gnu.org/licenses/>.
 */

/** Base class for ThWboard migrations
 *
 * The Migration class implements the basic interface to an ThWboard migration.
 * This includes the version this migration requires as baseline, the target
 * version it raises the application to.
 * The author and end user notes.
 */
abstract class Migration
{
    /** The ThWboard version this migration requires to be run successfully. */
    private $fromVersion;

    /** The ThWboard version this migration establishes. */
    private $toVersion;

    /** The responsible author of this migration. */
    private $author;

    /** Additional informations, that an administrator should know about
     * migration.
     */
    private $notes;

    /** Create a new Migration instance.
     *
     * @param string $fromVersion The baseline version of the ThWboard instance.
     * @param string $toVersion The target version of the ThWboard instance.
     * @param string $author The author responsible for this migration.
     * @param string $author Additional end user informations about the migration.
     */
    protected function __construct($fromVersion, $toVersion, $author, $notes)
    {
        $this->fromVersion = $fromVersion;
        $this->toVersion = $toVersion;
        $this->author = $author;
        $this->notes = $notes;
    }

    /** Executes an upgrade of the ThWboard instance.
     *
     * @param PDO $pdo A database connection. It must have a prefix property
     *   containing the table prefix of the ThWboard instance.
     *
     * @throws RuntimeException if the upgrade couldn't be run due to
     *   unsatisfied pre-conditions.
     * @throws PDOException if the database queries couldn't be executed.
     */
    abstract public function upgrade(PDO $pdo);

    /** Executes an downgrade of the ThWboard instance.
     *
     * @param PDO $pdo A database connection. It must have a prefix property
     *   containing the table prefix of the ThWboard instance.
     *
     * @throws RuntimeException if the downgrade couldn't be run due to
     *   unsatisfied pre-conditions.
     * @throws PDOException if the database queries couldn't be executed.
     */
    abstract public function downgrade(PDO $pdo);

    /** Magic method to access the Migration attributes.
     *
     * This method triggers an error if an unknown attribute is requested.
     *
     * @param string $name The attribute name.
     * @return The value of the given attribute.
     *
     * @see Migration::$fromVersion
     * @see Migration::$toVersion
     * @see Migration::$autor
     * @see Migration::$notes
     */
    public function __get($name)
    {
        if (!property_exists($this, $name)) {
            trigger_error('Requesting undefined object property '.__CLASS__.'::'.$name, E_USER_ERROR);
        }

        return $this->$name;
    }
}
