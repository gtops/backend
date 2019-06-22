import { isNaN } from "lodash";
import { config } from "../config/Config";

export class Tool {
	public static isUid(uid: string): boolean {
		const mask = config.other.uidMask;
		if (uid.length !== mask.length) {
			return false;
		}

		const maskArray = mask.split("");
		const uidArray = uid.split("");
		for (let index = 0; index < mask.length; index++) {
			if ((maskArray[index] === "*" && isNaN(+uidArray[index])) ||
				(maskArray[index] === "-" && uidArray[index] !== "-")) {
				return false;
			}
		}

		return true;
	}
}
