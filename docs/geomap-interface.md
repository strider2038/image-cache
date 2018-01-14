# Multipurpose interface for requesting geographical static map images

## Image caching geographical map parameters

| Name                   | Format                             | Short alias | Possible values                             | Default value    |
| ---------------------- | ---------------------------------- | ----------- | ------------------------------------------- | ---------------- |
| Latitude and longitude | center[=]{latitude},{longitude}    | c           | Latitude: [-90; 90], longitude: [-180; 180] | 0, 0             |
| Zoom                   | zoom[=]{zoom}                      | z           | [1; 20]                                     | 14               |
| Image size             | size[=]{width}[x{height}]          | s           | Width: [50; 640], height [50; 640]          | 600, 450         |
| Map type               | {roadmap/satellite/hybrid/terrain} |             |                                             | roadmap          |
| Scale                  | scale[=]{scale}                    | sc          | 1.0-4.0                                     | 1.0              |
| Extension              | {filename}.{jpg/jpeg/png}          |             |                                             | *defined by url* |

## Conversion to Yandex static map parameters

| Parameter              | Yandex map parameter      | Possible values                                                     |
| ---------------------- | ------------------------- | ------------------------------------------------------------------- |
| Latitude and longitude | ll={longitude},{latitude} | Latitude: [-90; 90], longitude: [-180; 180]                         |
| Zoom                   | z={zoom}                  | [1; 17]                                                             |
| Size                   | size={width},{height}     | Width: [1; 600], height: [1; 450]                                   |
| Map type               | l={map,sat,skl,trf}       | roadmap=map, satellite=sat, hybrid=map+sat, terrain not implemented |
| Scale                  | scale={scale}             | [1.0; 4.0]                                                          |
| Extension              | *ignored*                 | *response can contain JPEG or PNG image*                            |

## Conversion to Google static map parameters

| Parameter              | Google map parameter                         | Possible values                             |
| ---------------------- | -------------------------------------------- | ------------------------------------------- |
| Latitude and longitude | center={latitude},{longitude}                | Latitude: [-90; 90], longitude: [-180; 180] |
| Zoom                   | zoom={zoom}                                  | [1; 20]                                     |
| Size                   | size={width}x{height}                        | Width: [1; 640], height: [1; 640]           |
| Map type               | maptype={roadmap,satellite,hybrid,terrain}   | roadmap, satellite, hybrid, terrain         |
| Scale                  | scale={scale}                                | 1, 2                                        |
| Extension              | format={png,png8,png32,gif,jpg,jpg-baseline} | png, jpg                                    |
