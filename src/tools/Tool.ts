import { isNaN } from "lodash";
import { Config } from "../config";

export class Tool {
	public static isUid(uid: string): boolean {
		const mask = Config.other.uidMask;
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
