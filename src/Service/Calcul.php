<?php

namespace App\Service;

use Symfony\Component\Form\AbstractType;

class Calcul
{
    // Calcul de la distance en Kilometre entre 2 points
    public function distancePoints($lat1, $lng1, $lat2, $lng2) {
        $earth_radius = 6378137;   // Terre = sphère de 6378km de rayon
        $rlo1 = deg2rad($lng1);
        $rla1 = deg2rad($lat1);
        $rlo2 = deg2rad($lng2);
        $rla2 = deg2rad($lat2);
        $dlo = ($rlo2 - $rlo1) / 2;
        $dla = ($rla2 - $rla1) / 2;
        $a = (sin($dla) * sin($dla)) + cos($rla1) * cos($rla2) * (sin($dlo) * sin($dlo));
        $d = 2 * atan2(sqrt($a), sqrt(1 - $a));
        //
        $meter = ($earth_radius * $d);
        $distanceKm = $meter / 1000; 

        return $meter / 1000;
    }

    public function prix($zoneDepart, $zoneArrivee, $distanceParRoute){
        $prix = 0.00;
        if($zoneDepart == "1" && $zoneArrivee == "1"){echo 'ZONE 1';
            $prix = 10.00;
        }
        elseif($zoneDepart == "4" || $zoneArrivee == "4"){ echo 'HORS ZONE'; // HORS ZONE xKm
            $prix = 10+(0.4*$distanceParRoute);
        }
        elseif($zoneDepart == "3" || $zoneArrivee == "3"){echo 'AEROPORT'; // AEROPORT
            $prix = 25;
        }
        elseif($zoneDepart == "2" || $zoneArrivee == "2" ){echo 'ZONE 2';
            $prix = 19.00;
        }

        return $prix;
    }

    // Définie latitude max/min et longitude max/min d'un carre
    public function latLngMinMax($carre) {
        foreach ($carre as $key => $point) {
            if($key == 0){
                $latitudeMax = $point['latitude'];
                $longitudeMax = $point['longitude'];
                $latitudeMin = $point['latitude'];
                $longitudeMin = $point['longitude'];
            }

            if($point['latitude'] > $latitudeMax){
                $latitudeMax = $point['latitude'];
            }

            if($point['latitude'] < $latitudeMin){
                $latitudeMin = $point['latitude'];
            }

            if($point['longitude'] > $longitudeMax){
                $longitudeMax = $point['longitude'];
            }

            if($point['longitude'] < $longitudeMin){
                $longitudeMin = $point['longitude'];
            }
        }

        $latLngMinMax = ['latitudeMax' => $latitudeMax, 'longitudeMax' => $longitudeMax, 'latitudeMin' => $latitudeMin, 'longitudeMin' => $longitudeMin];

        return $latLngMinMax;
    }

    public function adresseInCercle1($distanceAdresseAcentreZone1, $rayonZone1){
        $exist = false;
        if($distanceAdresseAcentreZone1 < $rayonZone1){
            $exist = true;
        }
        return $exist;
    }

    public function adresseInCercle2($distanceAdresseAcentreZone2, $rayonZone2){
        $exist = false;
        if($distanceAdresseAcentreZone2 < $rayonZone2){
            $exist = true;
        }
        return $exist;
    }

    public function adresseInAeroport($distanceAdresseAeroport, $rayonAeroport){
        $exist = false;
        if($distanceAdresseAeroport < $rayonAeroport){
            $exist = true;
        }
        return $exist;
    }    

    public function adresseInCarre1Zone1($lat, $lng){
        $exist = false;
        $carre1Zone1 = [
            array('latitude'=> '47.205646', 'longitude' => '-1.589081', 'lieu' => ''),
            array('latitude'=> '47.205646', 'longitude' => '-1.543009', 'lieu' => ''),
            array('latitude'=> '47.197848', 'longitude' => '-1.543009', 'lieu' => ''),
            array('latitude'=> '47.197848', 'longitude' => '-1.589081', 'lieu' => '')
        ];

        // Determine latitude max/min et longitude max/min du carre1Zone1
        $latLngMinMax_Carre1Zone1 = $this->latLngMinMax($carre1Zone1);
        
        // Depart en dessous de latitude max du Carre 1 Zone 1 ?
        if($lat < $latLngMinMax_Carre1Zone1['latitudeMax']){
            // Depart au dessus de latitude min du Carre 1 Zone 1 ?
            if($lat > $latLngMinMax_Carre1Zone1['latitudeMin']){
                // Depart a droite de longitude min du Carre 1 Zone 1 ?
                if($lng > $latLngMinMax_Carre1Zone1['longitudeMin']){
                    // Depart a gauche de longitude max du Carre 1 Zone 1 ?
                    if($lng < $latLngMinMax_Carre1Zone1['longitudeMax']){
                        $exist = true;
                    }
                }
            }
        }
        return $exist;
    }

    public function adresseInCarre2Zone1($lat, $lng){
        $exist = false;
        $carre2Zone1 = [
            array('latitude'=> '47.222060', 'longitude' => '-1.587863', 'lieu' => ''),
            array('latitude'=> '47.222060', 'longitude' => '-1.561325', 'lieu' => ''),
            array('latitude'=> '47.205646', 'longitude' => '-1.561325', 'lieu' => ''),
            array('latitude'=> '47.205646', 'longitude' => '-1.587863', 'lieu' => '')
        ];

        // Determine latitude max/min et longitude max/min du carre2Zone1
        $latLngMinMax_Carre2Zone1 = $this->latLngMinMax($carre2Zone1);
        
        // Depart en dessous de latitude max du Carre 2 Zone 1 ?
        if($lat < $latLngMinMax_Carre2Zone1['latitudeMax']){
            // Depart au dessus de latitude min du Carre 2 Zone 1 ?
            if($lat > $latLngMinMax_Carre2Zone1['latitudeMin']){
                // Depart a droite de longitude min du Carre 2 Zone 1 ?
                if($lng > $latLngMinMax_Carre2Zone1['longitudeMin']){
                    // Depart a gauche de longitude max du Carre 2 Zone 1 ?
                    if($lng < $latLngMinMax_Carre2Zone1['longitudeMax']){
                        $exist = true;
                    }
                }
            }
        }
        return $exist;
    }

    public function adresseInCarre3Zone1($lat, $lng){
        $exist = false;
        $carre3Zone1 = [
            array('latitude'=> '47.229537', 'longitude' => '-1.582920', 'lieu' => ''),
            array('latitude'=> '47.229537', 'longitude' => '-1.562888', 'lieu' => ''),
            array('latitude'=> '47.222060', 'longitude' => '-1.562888', 'lieu' => ''),
            array('latitude'=> '47.222060', 'longitude' => '-1.582920', 'lieu' => '')
        ];

        // Determine latitude max/min et longitude max/min du carre3Zone1
        $latLngMinMax_Carre3Zone1 = $this->latLngMinMax($carre3Zone1);
        
        // Depart en dessous de latitude max du Carre 3 Zone 1 ?
        if($lat < $latLngMinMax_Carre3Zone1['latitudeMax']){
            // Depart au dessus de latitude min du Carre 3 Zone 1 ?
            if($lat > $latLngMinMax_Carre3Zone1['latitudeMin']){
                // Depart a droite de longitude min du Carre 3 Zone 1 ?
                if($lng > $latLngMinMax_Carre3Zone1['longitudeMin']){
                    // Depart a gauche de longitude max du Carre 3 Zone 1 ?
                    if($lng < $latLngMinMax_Carre3Zone1['longitudeMax']){
                        $exist = true;
                    }
                }
            }
        }
        return $exist;
    }

    public function adresseInCarre4Zone1($lat, $lng){
        $exist = false;
        $carre4Zone1 = [
            array('latitude'=> '47.234101', 'longitude' => '-1.565708', 'lieu' => ''),
            array('latitude'=> '47.234101', 'longitude' => '-1.557743', 'lieu' => ''),
            array('latitude'=> '47.229537', 'longitude' => '-1.557743', 'lieu' => ''),
            array('latitude'=> '47.229537', 'longitude' => '-1.565708', 'lieu' => '')
        ];

        // Determine latitude max/min et longitude max/min du carre4Zone1
        $latLngMinMax_Carre4Zone1 = $this->latLngMinMax($carre4Zone1);
        
        // Depart en dessous de latitude max du Carre 4 Zone 1 ?
        if($lat < $latLngMinMax_Carre4Zone1['latitudeMax']){
            // Depart au dessus de latitude min du Carre 4 Zone 1 ?
            if($lat > $latLngMinMax_Carre4Zone1['latitudeMin']){
                // Depart a droite de longitude min du Carre 4 Zone 1 ?
                if($lng > $latLngMinMax_Carre4Zone1['longitudeMin']){
                    // Depart a gauche de longitude max du Carre 4 Zone 1 ?
                    if($lng < $latLngMinMax_Carre4Zone1['longitudeMax']){
                        $exist = true;
                    }
                }
            }
        }
        return $exist;
    }

    public function adresseInCarre5Zone1($lat, $lng){
        $exist = false;
        $carre5Zone1 = [
            array('latitude'=> '47.232991', 'longitude' => '-1.571610', 'lieu' => ''),
            array('latitude'=> '47.232991', 'longitude' => '-1.565708', 'lieu' => ''),
            array('latitude'=> '47.229537', 'longitude' => '-1.565708', 'lieu' => ''),
            array('latitude'=> '47.229537', 'longitude' => '-1.571610', 'lieu' => '')
        ];

        // Determine latitude max/min et longitude max/min du carre5Zone1
        $latLngMinMax_Carre5Zone1 = $this->latLngMinMax($carre5Zone1);
        
        // Depart en dessous de latitude max du Carre 5 Zone 1 ?
        if($lat < $latLngMinMax_Carre5Zone1['latitudeMax']){
            // Depart au dessus de latitude min du Carre 5 Zone 1 ?
            if($lat > $latLngMinMax_Carre5Zone1['latitudeMin']){
                // Depart a droite de longitude min du Carre 5 Zone 1 ?
                if($lng > $latLngMinMax_Carre5Zone1['longitudeMin']){
                    // Depart a gauche de longitude max du Carre 5 Zone 1 ?
                    if($lng < $latLngMinMax_Carre5Zone1['longitudeMax']){
                        $exist = true;
                    }
                }
            }
        }
        return $exist;
    }

    public function adresseInCarre1Zone2($lat, $lng){
        $exist = false;
        $carre1Zone2 = [
            array('latitude'=> '47.260435', 'longitude' => '-1.623534', 'lieu' => ''),
            array('latitude'=> '47.260435', 'longitude' => '-1.589127', 'lieu' => ''),
            array('latitude'=> '47.222801', 'longitude' => '-1.589127', 'lieu' => ''),
            array('latitude'=> '47.222801', 'longitude' => '-1.623534', 'lieu' => '')
        ];

        // Determine latitude max/min et longitude max/min du carre1Zone2
        $latLngMinMax_Carre1Zone2 = $this->latLngMinMax($carre1Zone2);
        
        // Depart en dessous de latitude max du Carre 1 Zone 2 ?
        if($lat < $latLngMinMax_Carre1Zone2['latitudeMax']){
            // Depart au dessus de latitude min du Carre 1 Zone 2 ?
            if($lat > $latLngMinMax_Carre1Zone2['latitudeMin']){
                // Depart a droite de longitude min du Carre 1 Zone 2 ?
                if($lng > $latLngMinMax_Carre1Zone2['longitudeMin']){
                    // Depart a gauche de longitude max du Carre 1 Zone 2 ?
                    if($lng < $latLngMinMax_Carre1Zone2['longitudeMax']){
                        $exist = true;
                    }
                }
            }
        }
        return $exist;
    }

    public function adresseInCarre2Zone2($lat, $lng){
        $exist = false;
        $carre2Zone2 = [
            array('latitude'=> '47.267767', 'longitude' => '-1.589127', 'lieu' => ''),
            array('latitude'=> '47.267767', 'longitude' => '-1.554531', 'lieu' => ''),
            array('latitude'=> '47.250457', 'longitude' => '-1.554531', 'lieu' => ''),
            array('latitude'=> '47.250457', 'longitude' => '-1.589127', 'lieu' => '')
        ];

        // Determine latitude max/min et longitude max/min du carre2Zone2
        $latLngMinMax_Carre2Zone2 = $this->latLngMinMax($carre2Zone2);
        
        // Depart en dessous de latitude max du Carre 2 Zone 2 ?
        if($lat < $latLngMinMax_Carre2Zone2['latitudeMax']){
            // Depart au dessus de latitude min du Carre 2 Zone 2 ?
            if($lat > $latLngMinMax_Carre2Zone2['latitudeMin']){
                // Depart a droite de longitude min du Carre 2 Zone 2 ?
                if($lng > $latLngMinMax_Carre2Zone2['longitudeMin']){
                    // Depart a gauche de longitude max du Carre 2 Zone 2 ?
                    if($lng < $latLngMinMax_Carre2Zone2['longitudeMax']){
                        $exist = true;
                    }
                }
            }
        }
        return $exist;
    }

    public function adresseInCarre3Zone2($lat, $lng){
        $exist = false;
        $carre3Zone2 = [
            array('latitude'=> '47.265557', 'longitude' => '-1.617663', 'lieu' => ''),
            array('latitude'=> '47.265557', 'longitude' => '-1.589127', 'lieu' => ''),
            array('latitude'=> '47.260435', 'longitude' => '-1.589127', 'lieu' => ''),
            array('latitude'=> '47.260435', 'longitude' => '-1.617663', 'lieu' => '')
        ];

        // Determine latitude max/min et longitude max/min du carre2Zone2
        $latLngMinMax_Carre3Zone2 = $this->latLngMinMax($carre3Zone2);
        
        // Depart en dessous de latitude max du Carre 3 Zone 2 ?
        if($lat < $latLngMinMax_Carre3Zone2['latitudeMax']){
            // Depart au dessus de latitude min du Carre 3 Zone 2 ?
            if($lat > $latLngMinMax_Carre3Zone2['latitudeMin']){
                // Depart a droite de longitude min du Carre 3 Zone 2 ?
                if($lng > $latLngMinMax_Carre3Zone2['longitudeMin']){
                    // Depart a gauche de longitude max du Carre 3 Zone 2 ?
                    if($lng < $latLngMinMax_Carre3Zone2['longitudeMax']){
                        $exist = true;
                    }
                }
            }
        }
        return $exist;
    }

    public function adresseInCarre4Zone2($lat, $lng){
        $exist = false;
        $carre4Zone2 = [
            array('latitude'=> '47.254920', 'longitude' => '-1.529807', 'lieu' => ''),
            array('latitude'=> '47.254920', 'longitude' => '-1.510232', 'lieu' => ''),
            array('latitude'=> '47.247358', 'longitude' => '-1.510232', 'lieu' => ''),
            array('latitude'=> '47.247358', 'longitude' => '-1.529807', 'lieu' => '')
        ];

        // Determine latitude max/min et longitude max/min du carre4Zone2
        $latLngMinMax_Carre4Zone2 = $this->latLngMinMax($carre4Zone2);
        
        // Depart en dessous de latitude max du Carre 4 Zone 2 ?
        if($lat < $latLngMinMax_Carre4Zone2['latitudeMax']){
            // Depart au dessus de latitude min du Carre 4 Zone 2 ?
            if($lat > $latLngMinMax_Carre4Zone2['latitudeMin']){
                // Depart a droite de longitude min du Carre 4 Zone 2 ?
                if($lng > $latLngMinMax_Carre4Zone2['longitudeMin']){
                    // Depart a gauche de longitude max du Carre 4 Zone 2 ?
                    if($lng < $latLngMinMax_Carre4Zone2['longitudeMax']){
                        $exist = true;
                    }
                }
            }
        }
        return $exist;
    }

    public function adresseInCarre5Zone2($lat, $lng){
        $exist = false;
        $carre5Zone2 = [
            array('latitude'=> '47.250881', 'longitude' => '-1.510232', 'lieu' => ''),
            array('latitude'=> '47.250881', 'longitude' => '-1.494805', 'lieu' => ''),
            array('latitude'=> '47.235705', 'longitude' => '-1.494805', 'lieu' => ''),
            array('latitude'=> '47.235705', 'longitude' => '-1.510232', 'lieu' => '')
        ];

        // Determine latitude max/min et longitude max/min du carre 5 Zone 2
        $latLngMinMax_Carre5Zone2 = $this->latLngMinMax($carre5Zone2);
        
        // Depart en dessous de latitude max du Carre 5 Zone 2 ?
        if($lat < $latLngMinMax_Carre5Zone2['latitudeMax']){
            // Depart au dessus de latitude min du Carre 5 Zone 2 ?
            if($lat > $latLngMinMax_Carre5Zone2['latitudeMin']){
                // Depart a droite de longitude min du Carre 5 Zone 2 ?
                if($lng > $latLngMinMax_Carre5Zone2['longitudeMin']){
                    // Depart a gauche de longitude max du Carre 5 Zone 2 ?
                    if($lng < $latLngMinMax_Carre5Zone2['longitudeMax']){
                        $exist = true;
                    }
                }
            }
        }
        return $exist;
    }
}
