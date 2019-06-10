import { AgeCategory } from "./age-category/AgeCategory";
import { Command } from "./command/Command";
import { Competition } from "./competition/Competition";
import { Gender } from "./gender/Gender";
import { GroupInStandardParent } from "./groupIn-standard-parent/GroupInStandardParent";
import { ParticipantOnCompetition } from "./participant-on-competition/ParticipantOnCompetition";
import { Participant } from "./participant/Participant";
<<<<<<< HEAD
import { ParticipantOnCompetition } from "./participantOnCompetition/ParticipantOnCompetition";
import { Position } from "./Position/Position";
import { RefereeOnTrialInCompetition } from "./RefereeOnTrialInCompetition/RefereeOnTrialInCompetition";
import { ResultGuide } from "./resultGuide/ResultGuide";
import { ResultParticipantOnTrial } from "./resultParticipantOnTrial/ResultParticipantOnTrial";
=======
import { ResultGuide } from "./result-guide/ResultGuide";
import { ResultParticipantOnTrial } from "./result-participant-on-trial/ResultParticipantOnTrial";
>>>>>>> 7034921bcfa2985730d8572f2d4183edafb44495
import { Role } from "./role/Role";
import { StandardParent } from "./standard-parent/StandardParent";
import { TrialOnCompetition } from "./trial-on-competition/TrialOnCompetition";
import { TrialOnGroup } from "./trial-on-group/TrialOnGroup";
import { Trial } from "./trial/Trial";
import { Unit } from "./unit/Unit";
import { User } from "./user/User";
import { WorkerOfUser } from "./workerOfUser/WorkerOfUser";
import { WorkerOfUserInCompetition } from "./WorkerOfUserInCompetition/WorkerOfUserInCompetition";

export const models = [
	User,
	AgeCategory,
	Competition,
	Command,
	Gender,
	Unit,
	TrialOnCompetition,
	TrialOnGroup,
	Trial,
	StandardParent,
	Role,
	GroupInStandardParent,
	Participant,
	ParticipantOnCompetition,
	ResultGuide,
	ResultParticipantOnTrial,
	Position,
	WorkerOfUser,
	WorkerOfUserInCompetition,
	RefereeOnTrialInCompetition
];
