import logging
import string
from geopy.geocoders import Nominatim

from trackdirect.common.Singleton import Singleton

class PidorDetector(Singleton):
    def __init__(self):
        self.logger = logging.getLogger(__name__)
        self.blackList = self._fillBlackList()
        self.geolocator = Nominatim(user_agent="trackdirect", domain='geoc.dead.guru')
        self.geoCache = []

    def _fillBlackList(self):
        """Fill block list with stations that should be ignored
        """
        block_list = []
        ascii_uppercase = list(string.ascii_uppercase)
        for letter in ascii_uppercase:
            block_list.append("R" + letter)

        for letter in ascii_uppercase:
            block_list.append("U" + letter)
            if letter == "I":
                break

        for num in range(0, 10):
            block_list.append("R" + str(num))

        # block_list.append("UU") Crimea

        return block_list

    def _detectByCoordinates(self, lat, long):
        crdString = str(round(lat, 4)) + ", " + str(round(long, 4))
        if crdString in self.geoCache:
            return True
        else:
            location = self.geolocator.reverse(query=crdString)
            if location is not None:
                    if 'address' in location.raw and 'country_code' in location.raw['address'] and location.raw['address']['country_code'] == 'ru':
                        self.geoCache.append(crdString)
                        return True
                    else:
                        return False
            return False

    def isPidor(self, callsign, lat, long):
        if any(callsign.startswith(item) for item in self.blackList):
            return True
        else:
            if lat is not None and long is not None and any(callsign.startswith(item) for item in ["UU", "XU", "UW", "XW", "XS", "ZA", "ZF", "US", "ZE", "UN", "ZC", "UO", "ZB", "UR", "XU", "ZD", "UL", "XR", "XL", "XN", "XH"]):
                return self._detectByCoordinates(lat, long)
            else:
                return False