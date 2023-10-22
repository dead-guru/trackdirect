import logging
import string
import json
import os
from geojson import Point, MultiPolygon, Feature
from turfpy.measurement import boolean_point_in_polygon

from trackdirect.common.Singleton import Singleton


class PidorDetector(Singleton):
    def __init__(self):
        self.logger = logging.getLogger(__name__)
        self.blackList = self._fillBlackList()
        file_path = os.path.join(os.path.dirname(__file__), "russia.json")
        with open(file_path, "r") as f:
            data = json.load(f)
            self.polygon = Feature(geometry=MultiPolygon(data['geometry']['coordinates']))

    def _fillBlackList(self):
        """Fill block list with stations that should be ignored
        """
        block_list = []
        ascii_uppercase = list(string.ascii_uppercase)
        for letter in ascii_uppercase:
            block_list.append("R" + letter)
            block_list.append("P-R" + letter)

        for letter in ascii_uppercase:
            block_list.append("U" + letter)
            block_list.append("P-U" + letter)
            if letter == "I":
                break

        for num in range(0, 10):
            block_list.append("R" + str(num))
            block_list.append("P-R" + str(num))

        # block_list.append("UU") Crimea
        block_list.append("MOSCOW")

        self.logger.info(block_list)

        return block_list

    def _detectByCoordinates(self, lat, long):
        point = Feature(geometry=Point((round(long, 5), round(lat, 5))))
        result = boolean_point_in_polygon(point, self.polygon)
        self.logger.info("Detected (" + str(round(lat, 5)) + ", " + str(round(long, 5)) + ") by coordinates: " + str(result))
        return result

    def isPidor(self, callsign, lat, long):
        if any(callsign.startswith(item) for item in self.blackList):
            self.logger.info("Detected by callsign: " + str(callsign))
            return True
        else:
            if lat is not None and long is not None and any(callsign.startswith(item) for item in ["UU", "XU", "UW", "XW", "XS", "ZA", "ZF", "US", "ZE", "UN", "ZC", "UO", "ZB", "UR", "XU", "ZD", "UL", "XR", "XL", "XN", "XH", "HD", "HF", "HC"]):
                self.logger.info("Detecting by coordinates: " + str(callsign))
                return self._detectByCoordinates(lat, long)
            else:
                return False