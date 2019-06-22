import { Schema } from "joi";
import { ERoles } from "@middleware/interfaces/ERoles";

export interface IRoutes {
	[name: string]: {
		handler: string;
		allowRoles: ERoles[];
		validate?: Schema;
	};
}
