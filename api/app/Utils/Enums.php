<?php

namespace App\Utils;

class Enums
{
    /**
     * Retorna uma lista de países.
     *
     * @return array
     */
    public static function getCountries()
    {
        return [
            'BRASIL',
            'ARGENTINA',
            'CHILE',
            'ESPANHA',
            'PORTUGAL',
            'ESTADOS UNIDOS',
            'CANADÁ',
            'MÉXICO',
            'FRANÇA',
            'ALEMANHA',
            'REINO UNIDO',
            'ITÁLIA',
            'JAPÃO',
            'CHINA',
            'RÚSSIA',
            'ÍNDIA',
            'ÁFRICA DO SUL',
            'AUSTRÁLIA',
            'COREIA DO SUL',
            'ARÁBIA SAUDITA'
        ];
    }

    /**
     * Retorna uma lista de tipos de combustíveis.
     *
     * @return array
     */
    public static function getFuelTypes()
    {
        return [
            'GASOLINA',
            'ÁLCOOL',
            'DIESEL',
            'ELÉTRICO',
            'FLEX',
            'HIBRIDO'
        ];
    }

    /**
     * Retorna uma lista de tipos de revisão de veículos.
     *
     * @return array
     */
    public static function getRevisionTypes()
    {
        return [
            'PREVENTIVA',
            'CORRETIVA'
        ];
    }

    /**
     * Retorna uma lista de sexos válidos.
     *
     * @return array
     */
    public static function getValidGenders()
    {
        return ['F', 'M'];
    }

    /**
     * Retorna uma lista de cores aleatórias para veículos.
     *
     * @return array
     */
    public static function getRandomColors()
    {
        return [
            'BRANCO',
            'PRETO',
            'PRATA',
            'CINZA',
            'VERMELHO',
            'AZUL',
            'AMARELO',
            'VERDE',
            'LARANJA',
            'ROSA',
            'BEGE',
            'DOURADO',
            'MARROM',
            'ROXO'
        ];
    }

    /**
     * Retorna uma lista de marcas de veículos pré-definidas.
     *
     * @return array
     */
    public static function getPredefinedBrands()
    {
        return [
            'VOLKSWAGEN',
            'CHEVROLET',
            'FIAT',
            'FORD',
            'HONDA',
            'TOYOTA',
            'RENAULT',
            'NISSAN',
            'NISSAN1',
            'NISSAN2',
            'NISSAN4',
            'NISSAN7',
            'BMW',
            'MERCEDES-BENZ',
            'AUDI',
            'KIA',
            'PEUGEOT',
            'HYUNDAI',
            'JEEP',
            'CHRYSLER',
            'CITROËN',
            'MITSUBISHI',
            'KIA1',
            'KIA2',
            'MAZDA',
            'LAND ROVER'
        ];
    }
}
